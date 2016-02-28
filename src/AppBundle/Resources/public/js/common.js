userProgress = {
    el:    null,
    url:   null,
    timer: null,
    delay: 3000,

    init: function($el) {
        this.el  = $el;
        this.url = $el.data('url');

        return this._setTimeout();
    },

    _setTimeout: function() {
        clearTimeout(this.timer);

        this.timer = setTimeout(this._update.bind(this), this.delay);

        return this;
    },

    _update: function() {
        var _this = this;

        $.getJSON(this.url)
            .success(function(response) {
                _this.el.html(response.view);

                response.stop || _this._setTimeout();
            })
            .fail(function() {
                _this.el.text('Opps! Something went wrong!');
            })
        ;

        return this;
    }
};

var $progressContainer = $('#user-progress');

if ($progressContainer.length) {
    userProgress.init($progressContainer);
}