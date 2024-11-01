;var TeRedirect = {
    isError: false,
    EVENT: function () {
        var self = this;

        jQuery(document).on('click', '.te-redirect-item-add', function () {
            self.ITEM.add(jQuery(this).parent());
            return false;
        });

        jQuery(document).on('click', '.te-redirect-item-remove', function () {
            jQuery(this).parent().remove();
            return false;
        });

        jQuery(document).on('click', '#te-redirect input[type=submit]', function () {
            self.VALIDATION.handle(self, jQuery(this).parent());
            if (TeRedirect.isError) {
                return false;
            }
        });
    },
    ITEM: {
        add: function (afterItem) {
            afterItem.after('<div class="te-redirect-item">\n' +
                '                        <input class="item-from" name="from[]" type="text" value=""\n' +
                '                               placeholder="'+translateTeRedirect.from+'">\n' +
                '                        -> <input class="item-to" name="to[]" type="text" value=""\n' +
                '                                  placeholder="'+translateTeRedirect.to+'">\n' +
                '<a class="te-redirect-item-remove" href="#">'+translateTeRedirect.remove+'</a> ' +
                '<a class="te-redirect-item-add" href="#">'+translateTeRedirect.add_item+'</a>' +
                '                    </div>');
        },
        getFrom: function () {
            return Array.prototype.map.call(document.getElementsByName("from[]"), function (item) {
                var url = item.value.trim();

                if ((url.length > 0) && (url.length === parseInt(url.lastIndexOf('/')) + 1)) {
                    url = url.substring(url.lastIndexOf('/'), -1);
                }

                return url;
            }).filter(function (item) {
                return item;
            });
        },
        getTo: function () {
            return Array.prototype.map.call(document.getElementsByName("to[]"), function (item) {
                var url = item.value.trim();

                if ((url.length > 0) && (url.length === parseInt(url.lastIndexOf('/')) + 1)) {
                    url = url.substring(url.lastIndexOf('/'), -1);
                }

                return url;
            }).filter(function (item) {
                return item;
            });
        }

    },
    VALIDATION: {
        handle: function (self, form) {
            self.PROCESSING.open(translateTeRedirect.checking);
            self.VALIDATION.errors.clear();
            self.VALIDATION.check.from(self, form);
            self.VALIDATION.check.to(self, form);
            self.PROCESSING.close();
        },
        check: {
            from: function (self, form) {
                var from = self.ITEM.getFrom(),
                    sorted_arr = from.slice().sort(),
                    errorUrls = [];

                for (var i = 0; i < sorted_arr.length - 1; i++) {
                    if (sorted_arr[i + 1] == sorted_arr[i]) {
                        errorUrls.push(sorted_arr[i]);
                    }
                }
                //console.log('error: from', errorUrls);
                if (errorUrls.length > 0) {
                    TeRedirect.VALIDATION.errors.add(form, errorUrls, translateTeRedirect.error_from);
                }
            },
            to: function (self, form) {
                var from = self.ITEM.getFrom(),
                    to = self.ITEM.getTo(),
                    errorUrls = self.VALIDATION.check.diff(from, to);
                //console.log('error: to', errorUrls);
                if (errorUrls.length > 0) {
                    TeRedirect.VALIDATION.errors.add(form, errorUrls, translateTeRedirect.error_to, true);
                }
            },
            diff: function (from, to) {
                var ret = [];
                from.sort();
                to.sort();
                for (var i = 0; i < from.length; i += 1) {
                    if (to.indexOf(from[i]) > -1) {
                        ret.push(from[i]);
                    }
                }
                return ret;
            }
        },
        errors: {
            add: function (form, urls, text, isTo = false) {
                urls = TeRedirect.VALIDATION.errors.unique(urls);

                jQuery(".item-from").each(function () {
                    // from
                    for (var i = 0; urls.length > i; i++) {
                        if (jQuery(this).val().search(urls[i]) !== -1) {
                            jQuery(this).addClass('error');
                            jQuery('.te-error').append('<div class="update-nag te-none">' +
                                '<strong>' + urls[i] + '</strong> - ' + text + '</div>');
                            TeRedirect.isError = true;
                        }
                    }
                });

                if(isTo){
                    jQuery(".item-to").each(function () {
                        // from
                        for (var i = 0; urls.length > i; i++) {
                            if (jQuery(this).val().search(urls[i]) !== -1) {
                                jQuery(this).addClass('error');
                            }
                        }
                    });
                }
            },
            clear: function () {
                TeRedirect.isError = false;
                jQuery('.te-error').html('');
                jQuery(".item-from").removeClass('error');
                jQuery(".item-to").removeClass('error');
            },
            unique(arr) {
                var obj = {};

                for (var i = 0; i < arr.length; i++) {
                    var str = arr[i];
                    obj[str] = true; // запомнить строку в виде свойства объекта
                }

                return Object.keys(obj); // или собрать ключи перебором для IE8-
            }
        },
    },
    PROCESSING: {
        open: function (text) {
            jQuery('te-redirect-processing-text').text(text);
            jQuery('.te-redirect-processing').addClass('active');
        },
        close: function () {
            jQuery('.te-redirect-processing').removeClass('active');
        }
    }
};

jQuery(function () {
    TeRedirect.EVENT();
});

