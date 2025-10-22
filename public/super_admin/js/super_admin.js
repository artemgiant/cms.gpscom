$(document).ready(function () {

    /**
     * Фалідація числових полів
     * @param data
     */
    function removeNegativeValue(data) {
        data.val(data.val().replace(/[^\d].+/, ""));
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    }

    /**
     * Тариф
     * @param data
     */
    function editTariff(data) {
        let id = $(data).data('id');
        let url = '_tariff/show';
        $.ajax({
            url: url,
            data: {'id': id},
            success: function (response) {
                let tariff = response.tariff;

                $('#tariffId').val(tariff.id);
                $('#tariffTitle').val(tariff.title);
                $('#tariffPrice').val(tariff.price);
                $('#tariffOperatorId').val(tariff.operator_id);

            }, error: function (response) {
                console.log(response)
            }
        });
    }

    $(document).on('click', '.tariffEditBtn', function () {
        editTariff(this);
    });


    /**
     * Поле введення ціни
     */
    $(document).on('keyup change', '.input_number', function () {
        removeNegativeValue($(this));
    });

    function editSim(data) {
        let id = $(data).data('id');
        let url = '_sim-card/show';
        $.ajax({
            url: url,
            data: {'id': id},
            success: function (response) {
                let simCard = response.simCard;

                $('#simId').val(simCard.id);
                $('.simPhone').val(simCard.phone);
                $('#simOperator').find('option[value="' + simCard.operator_id + '"]').attr("selected", "selected");

            }, error: function (response) {
                console.log(response)
            }
        });
    }

    $(document).on('click', '.simdCardEditBtn', function () {
        editSim(this);
    });

    /**
     * Пошук сім-карт
     */
    $(document).on('keyup', '#search_sim_cards', function () {

        let search_value = $(this).val();
        $.ajax({
            type: "GET",
            url: '_sim-card/search',
            data: {
                'search_value': search_value,
            },
            success: function (response) {
                $("#items").html(response['html']);
            },
        });

    });


    /**
     * ОБЄКТИ
     * @param data
     */

    /**
     * Створення обєкта
     */
    $(document).on('click', '.createEquipmentBtn', function (e) {
        e.preventDefault();

        var form = $('#form_equipment_store');
        var url = form.attr('action');
        var data = form.serializeArray();
        var modalId = '#createEquipments';

        ajaxForm(data, url, modalId);
    });


    /**
     * Відбраження даних для форми редагування даних
     * @param data
     */
    function editEquipment(data) {
        let id = $(data).data('id');
        let url = '_equipment/show';
        $.ajax({
            url: url,
            data: {'id': id},
            success: function (response) {
                let equipment = response.equipment;

                $('#equipmentId').val(equipment.id);
                $('#equipmentObject').val(equipment.object);
                $('.equipmentDevice').val(equipment.device);
                $('#equipmentImei').val(equipment.imei);
                $('.equipmentPhone').val(equipment.phone);
                $('.equipmentPhone2').val(equipment.phone2);
                $('.equipmentDateStart').val(equipment.date_start);
                $('#equipmentTariff').find('option[value="' + equipment.tariff_id + '"]').attr("selected", "selected");

            }, error: function (response) {
                console.log(response)
            }
        });
    }


    $(document).on('click', '.equipmentEditBtn', function () {
        editEquipment(this);
    });

    /**
     * Редагування обєкта
     */
    $(document).on('click', '.updateEquipmentBtn', function (e) {
        e.preventDefault();

        var form = $('#form_equipment_update');
        var url = form.attr('action');
        var data = form.serializeArray();
        var modalId = '#editEquipment';

        ajaxForm(data, url, modalId);
    });

    /**
     * Пошук об'єктів
     */
    $(document).on('keyup', '#search_equipments', function () {
        let search_value = $(this).val();
        $.ajax({
            type: "GET",
            url: '_equipment/search',
            data: {
                'search_value': search_value,
            },
            success: function (response) {
                $("#items").html(response['html']);
            },
        });
    });

    /**
     * КЛІЄНТИ
     */
    function ajaxForm(data, url, modalId) {
        $.ajax({
            url: url,
            data: data,
            type: "POST",
            success: function (response) {
                toastr.success(response.message);

                setTimeout(function () {
                    $(modalId).modal('hide');
                    location.reload();
                }, 1000);

            }, error: function (xhr) {
                $.each(xhr.responseJSON.errors, function (key, message) {
                    toastr.error(message);
                });
            }
        });
    }

    /**
     * Створення клієнта
     */
    $(document).on('click', '.createClientBtn', function (e) {
        e.preventDefault();

        var form = $('#form_client_store');
        var url = form.attr('action');
        var data = form.serializeArray();
        var modalId = '#createClient';

        ajaxForm(data, url, modalId);
    });

    /**
     * Відображення даних для редагування клієнта
     * @param data
     */
    function editClient(data) {
        let id = $(data).data('id');
        let url = '_client/show';
        $.ajax({
            url: url,
            data: {'id': id},
            success: function (response) {
                let client = response.client;

                $('#clientId').val(client.id);
                $('#clientType').find('option[value="' + client.client_type + '"]').attr("selected", "selected");
                $('#clientName').val(client.name);
                $('#clientAccount').val(client.account);
                $('#clientContractNumber').val(client.contract_number);
                $('.clientContractDate').val(client.contract_date);
                $('#clientPerson').val(client.person);
                $('.clientPhone').val(client.phone);
                $('#clientManager').find('option[value="' + client.manager + '"]').attr("selected", "selected");
                $('.clientAccountantPhone').val(client.accountant_phone);
                $('#clientEmail').val(client.email);
                $('#clientIdWialon').val(client.client_id);

            }, error: function (response) {
                console.log(response)
            }
        });
    }

    $(document).on('click', '.clientEditBtn', function () {
        editClient(this);
    });

    /**
     * Редагування клієнта
     */
    $(document).on('click', '.editClientBtn', function (e) {
        e.preventDefault();

        var form = $('#form_client_update');
        var url = form.attr('action');
        var data = form.serializeArray();
        var modalId = '#editClient';

        ajaxForm(data, url, modalId);
    });

    /**
     * Пошук клієнтів
     */
    $(document).on('keyup', '#search_clients', function () {
        let search_value = $(this).val();
        $.ajax({
            type: "GET",
            url: '_client/search',
            data: {
                'search_value': search_value,
            },
            success: function (response) {
                $("#items").html(response['html']);
            },
        });
    });

    $(document).on('click', '.client_tr .td', function () {
        var route = $(this).closest('.client_tr').data('route');

        window.location.href = route;
    });


    /**
     * Пошук вільного обладнання
     */
    $(document).on('keyup', '#search_free_equipments', function () {
        let search_value = $(this).val();
        $.ajax({
            type: "GET",
            url: '/_free-equipments/search',
            data: {
                'search_value': search_value,
            },
            success: function (response) {
                $("#free_equipments").html(response['html']);
            },
        });
    });

    /**
     * Піключити обладення
     */
    $(document).on('click', '.start_equipment', function () {
        var equipment_id = $(this).data('equipment_id');

        $.ajax({
            type: "GET",
            url: '/_start_equipment',
            data: {
                'equipment_id': equipment_id,
            },
            success: function (response) {
                var status = response.status;
                var message = response.message;
                var date_start = response.date_start;

                $('.dot_' + equipment_id).removeClass('label-danger').removeClass('label-secondary').addClass('label-success');
                $('.equipment_' + equipment_id).find('.date_start').text(date_start);
                $('.equipment_' + equipment_id).find('.date_end').text('');
                $('.equipment_' + equipment_id).removeClass('deactive_equipment')
                if (status) {
                    toastr.success(message);
                }

            },
        });
    });

    /**
     * Відключити обладнання клієнта
     */
    $(document).on('click', '.end_equipment', function () {
        var equipment_id = $(this).data('equipment_id');

        $.ajax({
            type: "GET",
            url: '/_end_equipment',
            data: {
                'equipment_id': equipment_id,
            },
            success: function (response) {
                var status = response.status;
                var message = response.message;
                var date_end = response.date_end;

                $('.dot_' + equipment_id).removeClass('label-success').removeClass('label-secondary').addClass('label-danger');
                $('.equipment_' + equipment_id).find('.date_end').text(date_end);
                $('.equipment_' + equipment_id).removeClass('deactive_equipment')

                if (status) {
                    toastr.success(message);
                }

            },
        });
    });

    /**
     * Деактивація обладнання
     */
    $(document).on('click', '.deactive_equipment', function () {
        var equipment_id = $(this).data('equipment_id');

        $.ajax({
            type: "GET",
            url: '/_deactive-equipment',
            data: {
                'equipment_id': equipment_id,
            },
            success: function (response) {
                var status = response.status;
                var message = response.message;
                var date_end = response.date_end;

                $('.dot_' + equipment_id).removeClass('label-success').removeClass('label-danger').addClass('label-secondary');
                $('.equipment_' + equipment_id).find('.date_end').text(date_end);
                $('.equipment_' + equipment_id).addClass('deactive_equipment')

                if (status) {
                    toastr.success(message);
                }

            },
        });
    })

    /**
     * Показати дані обладнання клієнта
     */
    $(document).on('click', '.show_equipment', function () {
        let id = $(this).data('equipment_id');
        let url = '/_show_equipment';
        $.ajax({
            url: url,
            data: {'id': id},
            success: function (response) {
                var equipment = response.equipment;
                var sensors = response.sensors;
                var installations = response.installations

                $('#equipmentId').val(equipment.id);
                $('#equipmentObject').val(equipment.object);
                $('.equipmentDevice').val(equipment.device)
                $('#equipmentImei').val(equipment.imei);
                $('.equipmentPhone').val(equipment.phone);
                $('.equipmentPhone2').val(equipment.phone2);
                $('.equipmentDateStart').val(equipment.date_start);
                $('#equipmentTariff').find('option[value="' + equipment.tariff_id + '"]').attr("selected", "selected");

                $('#sensorEquipmentId').val(equipment.id);
                $('#installationEquipmentId').val(equipment.id);

                /** Список Сенсорів */
                $('#sensors_items').append('');
                sensors.forEach(function (item) {
                    $('#sensors_items').append('' +
                        '<tr class="sensor_' + item.id + '">' +
                        '<td class="text-center">' + item.name + '</td>' +
                        '<td class="text-center">' + item.type + '</td>' +
                        '<td class="text-center">' + item.sensor_id + '</td>' +
                        '<td class="text-center">' +
                        '<a href="javascript:;" class="btn btn-sm btn-clean btn-icon sensorEditBtn" data-toggle="modal" data-target="#editSensor"  data-id="' + item.id + '"><i class="las la-edit"></i></a>' +
                        '<a href="javascript:;" class="btn btn-sm btn-clean btn-icon sensorDeleteBtn" data-id="' + item.id + '"><i class="las la-trash"></a>' +
                        '</td>' +
                        '</tr>');
                });

                /** Список Монтажних робіт */
                $('#installations_items').append('');
                installations.forEach(function (item) {
                    $('#installations_items').append('' +
                        '<tr class="installation_' + item.id + '">' +
                        '<td class="text-center">' + item.date_create + '</td>' +
                        '<td class="text-center">' + item.type + '</td>' +
                        '<td class="text-center">' + item.comment + '</td>' +
                        '<td class="text-center">' + item.price + '</td>' +
                        '<td class="text-center">' +
                        '<a href="javascript:;" class="btn btn-sm btn-clean btn-icon installationEditBtn" data-id="' + item.id + '"><i class="las la-edit"></i></a>' +
                        '<a href="javascript:;" class="btn btn-sm btn-clean btn-icon installationDeleteBtn" data-id="' + item.id + '"><i class="las la-trash"></a>' +
                        '</td>' +
                        '</tr>');
                });

                $('#updateEquipment').modal('show');

            }, error: function (response) {
                console.log(response)
            }
        });
    });


    /**
     * Перемістити обєкт: відкрити модал
     */
    $(document).on('click', '.move_equipment', function () {
        var equipment_id = $(this).data('equipment_id');

        $('#equipment_id').val(equipment_id);
        $('#moveEquipment').modal('show');
    });

    /**
     * Перемістити обєкт: зберегти форму
     */
    $(document).on('click', '.moveEquipmentBtn', function (e) {
        e.preventDefault();

        var equipment_id = $('#equipment_id').val();
        var form = $('#form_move_equipment');
        var url = form.attr('action');
        var data = form.serializeArray();
        var modalId = '#moveEquipment';

        ajaxForm(data, url, modalId);

        $('.equipment_' + equipment_id).remove();
    });

    /**
     * Видалити обєкт
     */
    $(document).on('click', '.delete_equipment', function () {
        var equipment_id = $(this).data('equipment_id');
        var tr = $(this).closest('.equipment_item');

        $.ajax({
            type: "GET",
            url: '/_delete_equipment',
            data: {
                'equipment_id': equipment_id,
            },
            success: function (response) {
                var status = response.status;
                var message = response.message;

                if (status) {
                    tr.remove();

                    toastr.success(message);
                }

            },
        });
    });

    /**
     * Создать и поключить обьєкт
     */
    $(document).on('click', '.clientEquipmentStore', function (e) {
        e.preventDefault();

        var form = $('#form_client_equipment_store');
        var url = form.attr('action');
        var data = form.serializeArray();
        var modalId = '#createEquipments';

        ajaxForm(data, url, modalId);
    });

    /**
     * Форма перегляду тарифа слієнта
     */
    $(document).on('click', '.clientTariffUpdate', function (e) {
        e.preventDefault();

        var id = $(this).data('tariff_id');
        var client_id = $(this).data('client_id');
        var clients_tariff_id = $(this).data('clients_tariff_id');
        let url = '/_client/tariff/show';
        $.ajax({
            url: url,
            data: {
                'id': id,
                'client_id': client_id,
                'clients_tariff_id': clients_tariff_id,
            },
            success: function (response) {
                let price = response.price;

                $('#tariffId').val(id);
                $('#tariffPrice').val(price);
                $('#clientsTariffId').val(clients_tariff_id);
                $('#updateClientTariff').modal('show');


            }, error: function (response) {
                console.log(response)
            }
        });
    });

    /**
     * Видалення тарифа слієнта
     */
    $(document).on('click', '.clientTariffDelete', function () {

        var tariff_id = $('#tariffId').val();
        var client_id = $('#clientId').val();
        let url = '/_client/tariff/delete';
        var csrf = $('meta[name="csrf-token"]').attr('content');
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': csrf}});

        $.ajax({
            url: url,
            type: "POST",
            data: {
                'csrf': csrf,
                'tariff_id': tariff_id,
                'client_id': client_id,
            },
            success: function (response) {
                var status = response.status;
                if (status) {
                    location.reload();
                }

            }, error: function (response) {
                console.log(response)
            }
        });
    });


    /**
     * СЕНСОР
     */

    /** Додати сенсор */
    $(document).on('click', '.addSensorBtn', function () {

        var form = $('#form_add_sensor');
        var url = form.attr('action');
        var data = form.serializeArray();

        $.ajax({
            url: url,
            data: data,
            type: "POST",
            success: function (response) {
                var status = response.status;
                var sensor = response.sensor;
                var message = response.message;

                if (status) {
                    $('#sensors_items').prepend('' +
                        '<tr class="sensor_' + sensor.id + '">' +
                        '<td class="text-center">' + sensor.name + '</td>' +
                        '<td class="text-center">' + sensor.type + '</td>' +
                        '<td class="text-center">' + sensor.sensor_id + '</td>' +
                        '<td class="text-center">' +
                        '<a href="javascript:;" class="btn btn-sm btn-clean btn-icon sensorEditBtn" data-toggle="modal" data-target="#editSensor"  data-id="' + sensor.id + '"><i class="las la-edit"></i></a>' +
                        '<a href="javascript:;" class="btn btn-sm btn-clean btn-icon sensorDeleteBtn" data-id="' + sensor.id + '"><i class="las la-trash"></a>' +
                        '</td>' +
                        '</tr>');

                    $('#addSensor').modal('hide');
                    form.find("input[type=text], textarea").val('');
                    toastr.success(message);
                }

            }, error: function (xhr) {
                $.each(xhr.responseJSON.errors, function (key, message) {
                    toastr.error(message);
                });
            }
        });
    });

    /** Показати дані сенсора */
    $(document).on('click', '.sensorEditBtn', function () {

        let id = $(this).data('id');
        let url = '/_sensor-show';
        $.ajax({
            url: url,
            data: {'id': id},
            success: function (response) {
                let sensor = response.sensor;

                $('#sensorId').val(sensor.id);
                $('#sensorEquipmentId').val(sensor.equipment_id);
                $('#sensorName').val(sensor.name);
                $('#sensorType').val(sensor.type);
                $('#sensorSensorId').val(sensor.sensor_id);

                $('#editSensor').modal('show');

            }, error: function (response) {
                console.log(response)
            }
        });
    });

    /**
     * Оновлення даних сенсора
     */
    $(document).on('click', '.updateSensorBtn', function () {

        var form = $('#form_update_sensor');
        var url = form.attr('action');
        var data = form.serializeArray();

        $.ajax({
            url: url,
            data: data,
            type: "POST",
            success: function (response) {
                var status = response.status;
                var sensor = response.sensor;
                var message = response.message;

                if (status) {

                    $('.sensor_' + sensor.id).replaceWith('' +
                        '<tr class="sensor_' + sensor.id + '">' +
                        '<td class="text-center">' + sensor.name + '</td>' +
                        '<td class="text-center">' + sensor.type + '</td>' +
                        '<td class="text-center">' + sensor.sensor_id + '</td>' +
                        '<td class="text-center">' +
                        '<a href="javascript:;" class="btn btn-sm btn-clean btn-icon sensorEditBtn" data-toggle="modal" data-target="#editSensor"  data-id="' + sensor.id + '"><i class="las la-edit"></i></a>' +
                        '<a href="javascript:;" class="btn btn-sm btn-clean btn-icon sensorDeleteBtn" data-id="' + sensor.id + '"><i class="las la-trash"></a>' +
                        '</td>' +
                        '</tr>');

                    $('#editSensor').modal('hide');
                    form.find("input[type=text], textarea").val('');
                    toastr.success(message);
                }

            }, error: function (xhr) {
                $.each(xhr.responseJSON.errors, function (key, message) {
                    toastr.error(message);
                });
            }
        });
    });

    /** Видалення сенсора */
    $(document).on('click', '.sensorDeleteBtn', function () {
        var id = $(this).data('id');
        var url = '/_sensor-delete';
        $.ajax({
            url: url,
            data: {'id': id},
            success: function (response) {
                var status = response.status;
                var message = response.message;

                if (status) {
                    $('.sensor_' + id).remove();
                    toastr.success(message);
                }


            }, error: function (response) {
                console.log(response)
            }
        });

    });

    /**
     * МОНТАЖНІ РОБОТИ
     */

    /** Додати монтажну роботу */
    $(document).on('click', '.addInstallationBtn', function () {
        var form = $('#form_add_installation');
        var url = form.attr('action');
        var data = form.serializeArray();

        $.ajax({
            url: url,
            data: data,
            type: "POST",
            success: function (response) {
                var status = response.status;
                var installation = response.installation;
                var message = response.message;

                if (status) {
                    $('#installations_items').prepend(
                        '<tr class="installation_' + installation.id + '">' +
                        '<td class="text-center">' + installation.date_create + '</td>' +
                        '<td class="text-center">' + installation.type + '</td>' +
                        '<td class="text-center">' + installation.comment + '</td>' +
                        '<td class="text-center">' + installation.price + '</td>' +
                        '<td class="text-center">' +
                        '<a href="javascript:;" class="btn btn-sm btn-clean btn-icon installationEditBtn" data-id="' + installation.id + '"><i class="las la-edit"></i></a>' +
                        '<a href="javascript:;" class="btn btn-sm btn-clean btn-icon installationDeleteBtn" data-id="' + installation.id + '"><i class="las la-trash"></a>' +
                        '</td>' +
                        '</tr>');

                    $('#addInstallation').modal('hide');
                    form.find("input[type=text], textarea").val('');
                    toastr.success(message);
                }

            }, error: function (xhr) {
                $.each(xhr.responseJSON.errors, function (key, message) {
                    toastr.error(message);
                });
            }
        });
    });

    /** Показати дані Монтажної роботи */
    $(document).on('click', '.installationEditBtn', function () {
        let id = $(this).data('id');
        let url = '/_installation-show';
        $.ajax({
            url: url,
            data: {'id': id},
            success: function (response) {
                let installation = response.installation;

                $('#installationId').val(installation.id);
                $('#installationEditEquipmentId').val(installation.equipment_id);
                $('.installationDateCreate').val(installation.date_create);
                $('#installationType').find('option[value="' + installation.type + '"]').attr("selected", "selected");
                $('#installationComment').val(installation.comment);
                $('#installationPrice').val(installation.price);

                $('#editInstallation').modal('show');

            }, error: function (response) {
                console.log(response)
            }
        });
    });

    /**
     * Оновлення даних Монтажних робіт
     */
    $(document).on('click', '.editInstallationBtn', function () {
        var form = $('#form_edit_installation');
        var url = form.attr('action');
        var data = form.serializeArray();

        $.ajax({
            url: url,
            data: data,
            type: "POST",
            success: function (response) {
                var status = response.status;
                var installation = response.installation;
                var message = response.message;

                if (status) {

                    console.log('installation', installation);

                    $('.installation_' + installation.id).replaceWith(
                        '<tr class="installation_' + installation.id + '">' +
                        '<td class="text-center">' + installation.date_create + '</td>' +
                        '<td class="text-center">' + installation.type + '</td>' +
                        '<td class="text-center">' + installation.comment + '</td>' +
                        '<td class="text-center">' + installation.price + '</td>' +
                        '<td class="text-center">' +
                        '<a href="javascript:;" class="btn btn-sm btn-clean btn-icon installationEditBtn" data-id="' + installation.id + '"><i class="las la-edit"></i></a>' +
                        '<a href="javascript:;" class="btn btn-sm btn-clean btn-icon installationDeleteBtn" data-id="' + installation.id + '"><i class="las la-trash"></a>' +
                        '</td>' +
                        '</tr>');

                    $('#editInstallation').modal('hide');
                    form.find("input[type=text], textarea").val('');
                    toastr.success(message);
                }

            }, error: function (xhr) {
                $.each(xhr.responseJSON.errors, function (key, message) {
                    toastr.error(message);
                });
            }
        });
    });

    /** Видалити монтажну роботу*/
    $(document).on('click', '.installationDeleteBtn', function () {
        var id = $(this).data('id');
        var url = '/_installation-delete';

        $.ajax({
            url: url,
            data: {'id': id},
            success: function (response) {
                var status = response.status;
                var message = response.message;

                if (status) {
                    $('.installation_' + id).remove();
                    toastr.success(message);
                }

            }, error: function (response) {
                $.each(xhr.responseJSON.errors, function (key, message) {
                    toastr.error(message);
                });
            }
        });
    });

    /**
     * Пошук обладнання клієнта
     */
    $(document).on('keyup', '#search_client_equipments', function () {
        let search_value = $(this).val();
        let client_id = $(this).data('client_id');
        $.ajax({
            type: "GET",
            url: '/_client/equipments/search',
            data: {
                'search_value': search_value,
                'client_id': client_id,
            },
            success: function (response) {
                $("#client_equipments").html(response['html']);
            },
        });
    });

    /**
     * Зміна кольору статуса повідомлення
     * @param status
     * @param select
     */
    function changeSelectColor(status, select) {
        var button = select.next();
        var style = select.data('style');
        button.removeClass(style);

        console.log('status', status);
        if (status == 'opened') {
            button.addClass('btn-primary');
            select.data('style', 'btn-primary');
        } else {
            button.addClass('btn-success');
            select.data('style', 'btn-success');
        }
    }

    /**
     * Зміна стутуса повідомлення
     */
    $(document).on('change', '.notification_status', function () {

        var select = $(this);
        var status = select.find(":selected").val();
        var id = select.data('id');

        changeSelectColor(status, select)

        if (status && id) {
            $.ajax({
                type: "GET",
                url: '_notification/status',
                data: {
                    'status': status,
                    'id': id
                }
            });
        }

    });

    /**
     * Пошук повідомлень
     */
    $(document).on('keyup', '#search_notification', function () {
        let search_value = $(this).val();
        let client_id = $(this).data('client_id');
        $.ajax({
            type: "GET",
            url: '_notification/search',
            data: {
                'search_value': search_value,
            },
            success: function (response) {
                $("#items").html(response['html']).promise().done(function () {
                    $('.table-responsive .selectpicker').selectpicker();
                });
            },
        });
    });

    /**
     * Завантажити файл для імпорту Sim-Card
     */
    $(document).on('click', '.sim_cards_file_btn', function (e) {
        e.preventDefault();
        $('.sim_cards_file').click();
    });

    $(document).on('change', '.sim_cards_file', function () {
        var filename = $('.sim_cards_file').val().replace(/C:\\fakepath\\/i, '');
        $('.sim_cards_label').text(filename);
    });

    /**
     * Користучачі
     */

    /** Створити користувача */
    $(document).on('click', '.createUserBtn', function (e) {
        e.preventDefault();

        var form = $('#form_user_store');
        var url = form.attr('action');
        var data = form.serializeArray();
        var modalId = '#createUser';

        ajaxForm(data, url, modalId);
    });

    /**
     * Відображення даних для редагування користувача
     * @param data
     */
    function editUser(data) {
        let id = $(data).data('id');
        let url = '_user/show';
        $.ajax({
            url: url,
            data: {'id': id},
            success: function (response) {
                let user = response.user;

                $('#userId').val(user.id);
                $('#userName').val(user.name);
                $('#userSurname').val(user.surname);
                $('#userRole').find('option[value="' + user.role_id + '"]').attr("selected", "selected");
                $('.userPhone').val(user.phone);
                $('#userEmail').val(user.email);

            }, error: function (response) {
                console.log(response)
            }
        });
    }

    $(document).on('click', '.userEditBtn', function () {
        editUser(this);
    });

    /** Редагування користувача */
    $(document).on('click', '.editUserBtn', function (e) {
        e.preventDefault();

        var form = $('#form_user_edit');
        var url = form.attr('action');
        var data = form.serializeArray();
        var modalId = '#editUser';

        ajaxForm(data, url, modalId);
    });

    /**
     * Edit operator
     * @param data
     */
    function editOperator(data) {
        let id = $(data).data('id');
        let url = '_operator/show';
        $.ajax({
            url: url,
            data: {'id': id},
            success: function (response) {
                let operator = response.operator;

                $('#operatorId').val(operator.id);
                $('#operatorTitle').val(operator.title);

            }, error: function (response) {
                console.log(response)
            }
        });
    }

    $(document).on('click', '.operatorEditBtn', function () {
        editOperator(this);
    });

    $(document).on('change', '#reportType', function () {
        var type = $(this).val();
        $('#reportingFormType').val(type);
    });

    $(document).on('change', '#reportClientType', function () {
        var type = $(this).val();
        $('#reportingFormClientType').val(type);
    });

    $(document).on('change', '.reportMount', function () {
        var type = $(this).val();
        $('#reportingFormMount').val(type);
    });

    $(document).on('click', '.deleteUserBtn', function () {
        var user_id = $(this).data('user_id');
        $('#deleteUserId').val(user_id);

        $("#usersList option").show();
        $("#usersList option[value=" + user_id + "]").hide();
    });

    $(document).on('click', '.searchObjectImei', function () {
        $('.searchIcon').css('display', 'none');
        $(this).addClass('spinner spinner-white-success spinner-left');
    });

    $(document).on('click', '.updateClientDataBtn', function (){
        $(this).addClass('spinner');
    });
});










