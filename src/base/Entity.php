<?php

namespace dekey\domain\base;

use Deefour\Interactor\Context;
use dekey\base\Model;
use dekey\db\Transacting;
use dekey\di\contracts\ContainerAware;
use dekey\di\contracts\ServiceLocatorAware;
use dekey\di\mixins\ContainerAccess;
use dekey\di\mixins\ServiceLocatorAccess;
use dekey\domain\contracts\DomainEntity;
use dekey\domain\contracts\EntityDataSource;
use dekey\domain\contracts\LoggerAware;
use yii\base\ModelEvent;
use dekey\domain\mixins\LoggerAccess;
/**
 * Implements domain entity.
 *
 * @package dekey\domain
 * @author Dmitry Kolodko <dangel@quartsoft.com>
 */
class Entity extends Component implements DomainEntity {
    const EVENT_BEFORE_SAVE = 'beforeSave';
    /**
     * @event AfterSaveEvent an event that is triggered after a record is saved.
     */
    const EVENT_AFTER_SAVE = 'afterSave';
    /**
     * @event ModelEvent an event that is triggered before deleting a record.
     * You may set [[ModelEvent::isValid]] to be false to stop the deletion.
     */
    const EVENT_BEFORE_DELETE = 'beforeDelete';
    /**
     * @event Event an event that is triggered after a record is deleted.
     */
    const EVENT_AFTER_DELETE = 'afterDelete';
    /**
     * @var \dekey\db\ActiveRecord|\ClarityBaseModel
     */
    private $_dataSource;
    /**
     * @var array validation errors (attribute name => array of errors)
     */
    protected $_errors;

    public function getId() {
        return $this->dataSource->primaryKey;
    }

    /**
     * Populates the {@lint _dataSource} with input data.
     *
     * This method provides a convenient shortcut for:
     *
     * ```php
     * if (isset($_POST['FormName'])) {
     *     $model->attributes = $_POST['FormName'];
     *     if ($model->save()) {
     *         // handle success
     *     }
     * }
     * ```
     *
     * which, with `load()` can be written as:
     *
     * ```php
     * if ($model->load($_POST) && $model->save()) {
     *     // handle success
     * }
     * ```
     *
     * `load()` gets the `'FormName'` from the model's [[formName()]] method (which you may override), unless the
     * `$formName` parameter is given. If the form name is empty, `load()` populates the model with the whole of `$data`,
     * instead of `$data['FormName']`.
     *
     * Note, that the data being populated is subject to the safety check by [[setAttributes()]].
     *
     * @param array $data the data array to load, typically `$_POST` or `$_GET`.
     * @param string $formName the form name to use to load the data into the model.
     * If not set, [[formName()]] is used.
     * @return boolean whether `load()` found the expected form in `$data`.
     */
    public function load($data, $formName = null) {
        return $this->dataSource->load($this->convertDataToSourceAttributes($data), $formName);
    }

    /**
     * Converts data passed to {@link load()} into {@link _dataSource} attributes.
     * Override this method to implement specific logic for your entity.
     *
     * @param mixed $data traversable data of {@link _dataSource}.
     * @return mixed converted data. By default returns the same data as passed.
     */
    protected function convertDataToSourceAttributes(&$data) {
        return $data;
    }

    public function saveUsingTransaction($runValidation = true, $attributeNames = null) {
        return $this->callTransactionalMethod('save', $runValidation, $attributeNames);
    }

    public function save($runValidation = true, $attributeNames = null) {
        if (!$this->beforeSave()) {
            return false;
        }
        $result = $this->dataSource->save($runValidation, $attributeNames);
        if (!$result) {
            $this->setErrors($this->dataSource->getErrors());
        }

        $this->afterSave();

        return $result;
    }

    public function deleteUsingTransaction() {
        return $this->callTransactionalMethod('delete');
    }

    public function delete() {
        if (!$this->beforeDelete()) {
            return false;
        }
        $result = $this->dataSource->delete();
        if (!$result) {
            $this->setErrors($this->dataSource->getErrors());
        }

        $this->afterDelete();

        return $result;
    }

    public function validate($attributeNames = null, $clearErrors = true) {
        if (!$this->beforeValidate()) {
            return false;
        }

        $result = $this->dataSource->validate();
        if (!$result) {
            $this->setErrors($this->dataSource->getErrors());
        } elseif ($clearErrors) {
            $this->clearErrors();
        }

        $this->afterValidate();

        return $result;
    }



    /**
     * This method is called at the beginning of inserting or updating a record.
     * The default implementation will trigger an [[EVENT_BEFORE_INSERT]] event when `$insert` is true,
     * or an [[EVENT_BEFORE_UPDATE]] event if `$insert` is false.
     * When overriding this method, make sure you call the parent implementation like the following:
     *
     * ```php
     * public function beforeSave($insert)
     * {
     *     if (parent::beforeSave($insert)) {
     *         // ...custom code here...
     *         return true;
     *     } else {
     *         return false;
     *     }
     * }
     * ```
     *
     * @param boolean $insert whether this method called while inserting a record.
     * If false, it means the method is called while updating a record.
     * @return boolean whether the insertion or updating should continue.
     * If false, the insertion or updating will be cancelled.
     */
    protected function beforeSave() {
        $event = new ModelEvent;
        $this->trigger(self::EVENT_BEFORE_SAVE, $event);

        return $event->isValid;
    }

    /**
     * This method is called at the end of inserting or updating a record.
     * The default implementation will trigger an [[EVENT_AFTER_INSERT]] event when `$insert` is true,
     * or an [[EVENT_AFTER_UPDATE]] event if `$insert` is false. The event class used is [[AfterSaveEvent]].
     * When overriding this method, make sure you call the parent implementation so that
     * the event is triggered.
     *
     * @param boolean $insert whether this method called while inserting a record.
     * If false, it means the method is called while updating a record.
     * @param array $changedAttributes The old values of attributes that had changed and were saved.
     * You can use this parameter to take action based on the changes made for example send an email
     * when the password had changed or implement audit trail that tracks all the changes.
     * `$changedAttributes` gives you the old attribute values while the active record (`$this`) has
     * already the new, updated values.
     */
    protected function afterSave() {
        $this->trigger(self::EVENT_AFTER_SAVE, new ModelEvent());
    }

    /**
     * This method is invoked before deleting a record.
     * The default implementation raises the [[EVENT_BEFORE_DELETE]] event.
     * When overriding this method, make sure you call the parent implementation like the following:
     *
     * ```php
     * public function beforeDelete()
     * {
     *     if (parent::beforeDelete()) {
     *         // ...custom code here...
     *         return true;
     *     } else {
     *         return false;
     *     }
     * }
     * ```
     *
     * @return boolean whether the record should be deleted. Defaults to true.
     */
    protected function beforeDelete() {
        $event = new ModelEvent();
        $this->trigger(self::EVENT_BEFORE_DELETE, $event);

        return $event->isValid;
    }

    /**
     * This method is invoked after deleting a record.
     * The default implementation raises the [[EVENT_AFTER_DELETE]] event.
     * You may override this method to do postprocessing after the record is deleted.
     * Make sure you call the parent implementation so that the event is raised properly.
     */
    protected function afterDelete() {
        $this->trigger(self::EVENT_AFTER_DELETE);
    }

    public function getDataSource() {
        return $this->_dataSource;
    }
    public function setDataSource(EntityDataSource $source) {
        $this->_dataSource = $source;
    }

    // OVERRIDDEN METHODS THAT PROXY CALLS TO DATA SOURCE METHODS
    public function attributes() {
        return $this->dataSource->attributes();
    }

    public function attributeLabels() {
        return $this->dataSource->attributeLabels();
    }

    public function attributeHints() {
        return $this->dataSource->attributeHints();
    }

    public function getAttributes($names = null, $except = []) {
        return $this->dataSource->getAttributes($names, $except);
    }

    public function activeAttributes() {
        return $this->dataSource->activeAttributes();
    }

    public function isAttributeRequired($attribute) {
        return $this->dataSource->isAttributeRequired($attribute);
    }

    public function isAttributeSafe($attribute) {
        return $this->dataSource->isAttributeSafe($attribute);
    }

    public function isAttributeActive($attribute) {
        return $this->dataSource->isAttributeActive($attribute);
    }

    public function getAttributeLabel($attribute) {
        return $this->dataSource->getAttributeLabel($attribute);
    }

    public function getAttributeHint($attribute) {
        return $this->dataSource->getAttributeHint($attribute);
    }

    public function setAttributes($values, $safeOnly = true) {
        $this->dataSource->setAttributes($values, $safeOnly);
    }

    public function safeAttributes() {
        return $this->dataSource->safeAttributes();
    }

    // OVERRIDDEN METHODS TO ACCESS PRIVATE DATA

    /**
     * Returns a value indicating whether there is any validation error.
     *
     * @param string|null $attribute attribute name. Use null to check all attributes.
     * @return boolean whether there is any error.
     */
    public function hasErrors($attribute = null) {
        return $attribute === null ? !empty($this->_errors) : isset($this->_errors[$attribute]);
    }

    /**
     * Returns the errors for all attribute or a single attribute.
     *
     * @param string $attribute attribute name. Use null to retrieve errors for all attributes.
     * @property array An array of errors for all attributes. Empty array is returned if no error.
     * The result is a two-dimensional array. See [[getErrors()]] for detailed description.
     * @return array errors for all attributes or the specified attribute. Empty array is returned if no error.
     * Note that when returning errors for all attributes, the result is a two-dimensional array, like the following:
     *
     * ```php
     * [
     *     'username' => [
     *         'Username is required.',
     *         'Username must contain only word characters.',
     *     ],
     *     'email' => [
     *         'Email address is invalid.',
     *     ]
     * ]
     * ```
     *
     * @see getFirstErrors()
     * @see getFirstError()
     */
    public function getErrors($attribute = null) {
        if ($attribute === null) {
            return $this->_errors === null ? [] : $this->_errors;
        } else {
            return isset($this->_errors[$attribute]) ? $this->_errors[$attribute] : [];
        }
    }

    /**
     * Returns the first error of every attribute in the model.
     *
     * @return array the first errors. The array keys are the attribute names, and the array
     * values are the corresponding error messages. An empty array will be returned if there is no error.
     * @see getErrors()
     * @see getFirstError()
     */
    public function getFirstErrors() {
        if (empty($this->_errors)) {
            return [];
        } else {
            $errors = [];
            foreach ($this->_errors as $name => $es) {
                if (!empty($es)) {
                    $errors[$name] = reset($es);
                }
            }

            return $errors;
        }
    }

    /**
     * Returns the first error of the specified attribute.
     *
     * @param string $attribute attribute name.
     * @return string the error message. Null is returned if no error.
     * @see getErrors()
     * @see getFirstErrors()
     */
    public function getFirstError($attribute) {
        return isset($this->_errors[$attribute]) ? reset($this->_errors[$attribute]) : null;
    }

    /**
     * Adds a new error to the specified attribute.
     *
     * @param string $attribute attribute name
     * @param string $error new error message
     */
    public function addError($attribute, $error = '') {
        $this->_errors[$attribute][] = $error;
    }

    /**
     * Adds a list of errors.
     *
     * @param array $items a list of errors. The array keys must be attribute names.
     * The array values should be error messages. If an attribute has multiple errors,
     * these errors must be given in terms of an array.
     * You may use the result of [[getErrors()]] as the value for this parameter.
     * @since 2.0.2
     */
    public function addErrors(array $items) {
        foreach ($items as $attribute => $errors) {
            if (is_array($errors)) {
                foreach ($errors as $error) {
                    $this->addError($attribute, $error);
                }
            } else {
                $this->addError($attribute, $errors);
            }
        }
    }

    /**
     * Adds a list of errors.
     *
     * @param array $items a list of errors.
     */
    public function setErrors(array $items) {
        $this->_errors = $items;
    }

    /**
     * Removes errors for all attributes or a single attribute.
     *
     * @param string $attribute attribute name. Use null to remove errors for all attribute.
     */
    public function clearErrors($attribute = null) {
        if ($attribute === null) {
            $this->_errors = [];
        } else {
            unset($this->_errors[$attribute]);
        }
    }

    /**
     * @deprecated use {@link isNew()} instead
     * @return bool
     */
    public function getIsNewRecord() {
        return $this->dataSource->isNewRecord;
    }

    public function isNew() {
        return $this->dataSource->isNewRecord;
    }
    public function isNotNew() {
        return $this->dataSource->isNewRecord;
    }

    public function hasAttribute($name) {
        return $this->dataSource->hasAttribute($name);
    }

    public function getAttribute($name) {
        return $this->dataSource->getAttribute($name);
    }


    // MAGIC ACCESS TO DATA SOURCE ATTRIBUTES

    public function __get($name) {
        try {
            $result = parent::__get($name);
        } catch (\Exception $e) {
            $dataSource = $this->getDataSource();
            if ($dataSource->canGetProperty($name)) {
                $result = $dataSource->$name;
            } else {
                throw $e;
            }
        }
        return $result;
    }

    public function __set($name, $value) {
        try {
            parent::__set($name, $value);
        } catch (\Exception $e) {
            $dataSource = $this->getDataSource();
            if ($dataSource&& $dataSource->canSetProperty($name)) {
                $dataSource->$name = $value;
            } else {
                throw $e;
            }
        }
    }

    public function __isset($name) {
        $result = parent::__isset($name);
        $dataSource = $this->getDataSource();
        if (!$result && $dataSource && $dataSource->canGetProperty($name)) {
            $result = isset($dataSource->$name);
        }
        return $result;
    }

    public function __unset($name) {
        try {
            parent::__unset($name);
        } catch (\Exception $e) {
            $dataSource = $this->getDataSource();
            if ($dataSource && $dataSource->canGetProperty($name)) {
                unset($dataSource->$name);
            } else {
                throw $e;
            }
        }
    }
}