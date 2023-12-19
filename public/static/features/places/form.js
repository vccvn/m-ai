

$(function () {
    const EsPlace = function EsPlace() {
        const $form = $('#add-place-form');
        var self = this;
        this.init = function init() {
            $form.on("submit", function (event) {
                event.preventDefault();
                App.api.post($form.attr('action'), new FormData($form[0]))
                    .then(rs => {
                        if (rs.status) {
                            App.htmlSelect.changeOptions('place_id', rs.data);
                            App.modal.hide("place-modal");
                            // self.showMenu();
                        } else {
                            var error = Object.keys(rs.errors).map(key => rs.errors[key]).join("<br />");
                            if (error) {
                                App.Swal.errorDetail(rs.message, error);
                            } else {
                                App.Swal.warning(rs.message);
                            }
                        }
                    })
                    .catch(e => {
                        App.Swal.error('Lỗi không xác định')
                    })
            });

            App.modal.on('hide', function (modal, id) {
                if (id == 'place-modal' && this.nextModal != "library-modal") {
                    setTimeout(() => {
                        self.showMenu();

                    }, 240);
                    // console.log(JSON.parse(JSON.stringify(this)))
                }
                if (id == 'library-modal') {
                    if (this.prevModal == 'place-modal') {
                        if (this.nextModal != "place-modal") {
                            setTimeout(() => {
                                self.showMenu();

                            }, 240);
                            // console.log(JSON.parse(JSON.stringify(this)))

                        }
                    }
                }
            })
            App.modal.on("show", function (modal, id) {

            });
        }

        this.clearFormData = function clearFormData() {
            console.log("Clear Form Data");
            $form.find('#place-name,#place-address').val('');
            App.htmlSelect.deactive('region_id');
            App.htmlSelect.deactive('district_id');
            App.htmlSelect.deactive('ward_id');
        }
        this.showForm = function showForm() {
            try {

                App.modal.show("place-modal")
                this.clearFormData();
                $form.find('#place-name').val(App.htmlSelect.getTag('place_id').keywords);
            } catch (error) {
                console.log(error);
            }
        }

        this.showResult = function showResult() {

        }

        this.showMenu = function showMenu() {
            $('#place_id-wrapper #place_id-dropdown').click()
            // $('#place_id-dropdown').dropdown();

            // console.log("showMenu")

        }
    }

    App.places = new EsPlace();

    App.places.init();
});
