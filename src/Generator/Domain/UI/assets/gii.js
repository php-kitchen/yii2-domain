yii.gii = (function ($, module) {
    var getNamespace = function () {
        return $('#generator-ns').val().split('\\');
    };

    var setNameSpace = function (namespace) {
        namespace = namespace.join('\\');
        $('#generator-ns').val(namespace)
        $('#domain-generator .field-generator-ns .sticky-value').html(namespace);
    };

    return {
        autocomplete: function (counter, data) {
            module.autocomplete(counter, data);
        },
        init: function () {
            module.init();

            // domain generator: translate table name to domain name
            $('#domain-generator #generator-tablename').on('blur', function () {
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
                    // if table name contains two or more words
                    if (words.length > 1) {
                        // set first word as module name
                        var firstWord = words[0];
                        var moduleName = firstWord.substring(0, 1).toUpperCase() + firstWord.substring(1);
                        $('#generator-modulename').val(moduleName).blur();
                    }
                    // set rest words as domain name
                    var domainName = '';
                    $.each(words.splice(1), function () {
                        if (this.length > 0) {
                            domainName += this.substring(0, 1).toUpperCase() + this.substring(1);
                        }
                    });
                    $('#generator-domainname').val(domainName).blur();
                }
            });

            // domain generator: synchronize model name with domain namespace
            $('#domain-generator #generator-modulename').on('blur', function () {
                var moduleName = $(this).val();
                if (moduleName) {
                    var namespace = getNamespace();
                    namespace[0] = moduleName;
                    setNameSpace(namespace);
                }
            });
        }
    }
})(jQuery, yii.gii);