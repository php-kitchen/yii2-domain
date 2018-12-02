yii.gii = (function ($, module) {
    var formId = '#domain-model-generator';
    var generatorName = 'modelgenerator';

    var getNamespace = function () {
        return $('#' + generatorName + '-ns').val().split('\\');
    };

    var setNameSpace = function (namespace) {
        namespace = namespace.join('\\');
        $('#' + generatorName + '-ns').val(namespace);
        $(formId + ' .field-' + generatorName + '-ns .sticky-value').html(namespace);
    };

    return {
        autocomplete: function (counter, data) {
            module.autocomplete(counter, data);
        },
        init: function () {
            module.init();

            // model generator: hide class name inputs when table name input contains
            $(formId + ' #' + generatorName + '-tablename').change(function () {
                var show = ($(this).val().indexOf('*') === -1);
                $('.field-' + generatorName + '-modelclass').toggle(show);
                if ($('#' + generatorName + '-generatequery').is(':checked')) {
                    $('.field-' + generatorName + '-queryclass').toggle(show);
                }
            }).change();

            // model generator: translate table name to domain name
            $(formId + ' #' + generatorName + '-tablename').on('blur', function () {
                var tableName = $(this).val();
                var tablePrefix = $(this).attr('table_prefix') || '';
                if (tablePrefix.length) {
                    // if starts with prefix
                    if (tableName.slice(0, tablePrefix.length) === tablePrefix) {
                        // remove prefix
                        tableName = tableName.slice(tablePrefix.length);
                    }
                }

                if (tableName && tableName.indexOf('*') === -1) {
                    var words = tableName.split(/\.|\_/);

                    // set first word as module name
                    var firstWord = words[0];
                    var moduleName = firstWord.substring(0, 1)
                                              .toUpperCase() + firstWord.substring(1);

                    // if table name contains two or more words
                    if (words.length > 1) {
                        // set rest words as domain name
                        var domainName = '';
                        $.each(words.splice(1), function () {
                            if (this.length > 0) {
                                domainName += this.substring(0, 1)
                                                  .toUpperCase() + this.substring(1);
                            }
                        });
                    } else {
                        domainName = moduleName;
                    }

                    $('#' + generatorName + '-modulename').val(moduleName).blur();
                    $('#' + generatorName + '-domainname').val(domainName).blur();
                }
            });

            // model generator: translate model class to query class
            $(formId + ' #' + generatorName + '-modelclass').on('blur', function () {
                var modelClass = $(this).val();
                if (modelClass !== '') {
                    var queryClass = $('#' + generatorName + '-queryclass').val();
                    if (queryClass === '') {
                        queryClass = modelClass + 'Query';
                        $('#' + generatorName + '-queryclass').val(queryClass);
                    }
                }
            });

            // model generator: synchronize model name with domain namespace
            $(formId + ' #' + generatorName + '-modulename').on('blur', function () {
                var moduleName = $(this).val();
                if (moduleName) {
                    var namespace = getNamespace();
                    namespace[0] = moduleName;
                    setNameSpace(namespace);
                }
            });

            // model generator: toggle query fields
            $('form #' + generatorName + '-generatequery').change(function () {
                $('form .field-' + generatorName + '-queryns').toggle($(this).is(':checked'));
                $('form .field-' + generatorName + '-queryclass').toggle($(this).is(':checked'));
                $('form .field-' + generatorName + '-querybaseclass')
                .toggle($(this).is(':checked'));
                $('#' + generatorName + '-queryclass')
                .prop('disabled', $(this).is(':not(:checked)'));
            }).change();

            // hide message category when I18N is disabled
            $('form #' + generatorName + '-enablei18n').change(function () {
                $('form .field-' + generatorName + '-messagecategory')
                .toggle($(this).is(':checked'));
            }).change();

            // hide Generate button if any input is changed
            $('.default-view-results,.default-view-files').show();
            $('.default-view button[name="generate"]').show();
            $('#form-fields').find('input,select,textarea').change(function () {
                $('.default-view-results,.default-view-files').hide();
                $('.default-view button[name="generate"]').hide();
            });

            $('.module-form #' + generatorName + '-moduleclass').change(function () {
                var value = $(this).val().match(/(\w+)\\\w+$/);
                var $idInput = $('#' + generatorName + '-moduleid');
                if (value && value[1] && $idInput.val() === '') {
                    $idInput.val(value[1]);
                }
            });
        }
    }
})(jQuery, yii.gii);