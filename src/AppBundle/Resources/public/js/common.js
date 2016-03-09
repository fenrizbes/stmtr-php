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

                if (response.stop) {
                    $('#userbar').attr('src', $('#userbar').attr('src') +'?update=1');
                    $('#user-share-block, #user-bar-block').removeClass('hidden');
                } else {
                    _this._setTimeout();
                }
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

$('#user-bar-block input').on('focus click', function() {
    this.setSelectionRange(0, this.value.length);
});