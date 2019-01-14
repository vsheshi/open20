/** Javascript for global actions **/
var uscitaForm = false;
var formModificato = false;

$('form input, form select, form textarea').focus(function () {
    formModificato = true;
});


$('#form-actions > button[type="submit"]').on('click', this, function () {
    uscitaForm = true;
});

window.onbeforeunload = function () {
    if (uscitaForm == false && formModificato == true) {
        return 'Stai cercando di abbandonare la pagina senza salvare! Sei sicuro di voler procedere?';
    }
}

var FormActions = {};
FormActions.launchListenTab = function () {
    //se vi era una preferenza attiva, la setta
    FormActions.openTab();

    $("ul.nav-tabs > li > a").on("shown.bs.tab", function (e) {
        var tabid = $(e.target).attr("href");
        FormActions.setOpenTab(tabid);
    });
};
FormActions.openTab = function () {
    var tabid = window.location.hash;
    if (tabid) {
        $('a[href="' + tabid + '"]').tab('show');
    }
};
FormActions.setOpenTab = function (tabid) {
    //action del form
    var actionForm = $("form").attr("action");

    //rimuove eventuali # precedenti
    var actionFormTemp = actionForm.split("#");

    //accoda l'hash all'url del form
    var newAction = actionFormTemp[0] + tabid;
    $("form").attr("action", newAction);
};
FormActions.commonValidateForm = function (form_obj, messages) {

    //se erano presenti delle icone di errore sulle tab, le rimuove
    if (form_obj.find(".errore-alert").length > 0) {
        form_obj.find(".errore-alert").remove();
    }

    //cicla gli elementi
    $.each(messages, function (element, message) {
        //recupera l'elemento
        var element_obj = form_obj.find("#" + element);

        //se lo ha trovato
        if (element_obj && element_obj.length > 0) {
            //recupera la tab padre
            var tab_obj = element_obj.parents(".tab-pane").first();
            //se la ha trovata
            if (tab_obj && tab_obj.length > 0) {
                //id della tab padre
                var id_tab = tab_obj.attr("id");

                //recupera la tab vera e propria
                var tab_a = $(".nav-tabs").find('li a[href="#' + id_tab + '"]');

                //se la ha trovata
                if (tab_a && tab_a.length > 0) {
                    /* gestione della segnalazione di errore */

                    //verifica se la tab padre ha già la segnalazione di errore
                    var hasError = (tab_a.find(".errore-alert").length > 0 ? true : false);

                    //se trova un messaggio di errore e sulla tab padre non è già presente l'avviso                                                                                         
                    if (!hasError && message.length > 0) {
                        //recupera l'icona comune da clonare e piazzare qui dentro
                        var icon_common = $("#errore-alert-common");

                        //se l'ha trovata
                        if (icon_common && icon_common.length > 0) {
                            var icon_clone = icon_common.clone();
                            icon_clone.removeClass("bk-noDisplay");
                            icon_clone.removeAttr("id");

                            //inserisce l'icona
                            tab_a.append(icon_clone);
                        }
                        else {
                            alert("ICONA NON TROVATA");
                        }
                    }
                }
            }
        }
    });
};
FormActions.afterValidate = function () {
    var form_obj = $("form");

    //bind dell'evento di afterValidate dell'ActiveForm
    form_obj.on('afterValidate', function (e, messages) {
        FormActions.commonValidateForm(form_obj, messages);
    });

    //lancia subito per gli errori da submit
    var messages_submit = {};

    $.each($(".has-error"), function (index, row) {
        var input = $(row).find("input");
        var alert = $(row).find(".tooltip-error-field").find("span").first();
        messages_submit[input.attr("id")] = [alert.attr("data-original-title")];
    });

    FormActions.commonValidateForm(form_obj, messages_submit);
};

var Dashboard = {};
Dashboard.matrix = null;
Dashboard.save_url = null;
Dashboard.type = null;
Dashboard.id_dashboards = null;
Dashboard.id_button_dashboards = null;
Dashboard.dashboards_ul = null;
Dashboard.inError = false;
Dashboard.init = function (type, id_dashboards, saveUrl) {
    if (type && id_dashboards && saveUrl) {
        Dashboard.type = (type ? type : 'primary');
        Dashboard.id_dashboards = id_dashboards;
        Dashboard.dashboards_ul = $(Dashboard.id_dashboards);
        Dashboard.save_url = saveUrl;
        Dashboard.id_button_dashboards = "#bk-salvaAnnullaTools";
    }
    else {
        Dashboard.errorMsg();
    }
};
Dashboard.errorMsg = function () {
    alert("Errore di configurazione dashboard");
    Dashboard.inError = true;
};
Dashboard.enableDashboardsSortable = function () {
    //se la configurazione non è in errore
    if (!Dashboard.inError) {
        //attiva il sotable
        Dashboard.dashboards_ul.sortable();

        //salva la matrice originale degli elementi
        Dashboard.dashboard_matrix = Dashboard.getMatrixDashboards();

        //mostra la barra dei pulsanti
        Dashboard.showHideOrderButtons("show");
    }
    else {
        Dashboard.errorMsg();
    }
};
Dashboard.disableDashboardsSortable = function () {
    //attiva il sotable
    Dashboard.dashboards_ul.sortable('destroy');

    //esegue il restore degli elementi allo stato originale
    Dashboard.restoreMatrixDashboards();

    //azzera la variabile globale
    Dashboard.dashboard_matrix = null;

    //nasconde la barra dei pulsanti
    Dashboard.showHideOrderButtons("hide");
};
Dashboard.saveAndDisableDashboardsSortable = function () {
    //recupera la matrice attuale da salvare
    var matrix_to_save = Dashboard.getMatrixDashboards();

    //url su cui salvare
    var url_save_dashboard = Dashboard.save_url;

    $.ajax({
        url: url_save_dashboard,
        data: {matrix_to_save: matrix_to_save, dashboard_type: Dashboard.type},
        type: 'post',
        dataType: 'json',
        success: function (data) {
            //attiva il sotable
            Dashboard.dashboards_ul.sortable('destroy');

            //se il salvataggio non è andato a buon fine
            if (data.success == false) {
                alert("Errore di salvataggio dell'ordinamento");

                //esegue il restore degli elementi allo stato originale
                Dashboard.restoreMatrixDashboards();
            }

            //azzera la variabile globale
            Dashboard.dashboard_matrix = null;

            //nasconde la barra dei pulsanti
            Dashboard.showHideOrderButtons("hide");
        },
        error: function () {
            alert("Errore di comunicazione con il server");
        }
    });
};
Dashboard.showHideOrderButtons = function (action) {
    var buttonbar = $(Dashboard.id_button_dashboards);

    if (action == 'show') {
        buttonbar.show();
    }
    else {
        buttonbar.hide();
    }
};
Dashboard.getMatrixDashboards = function () {
    //array di ritorno
    var array_return = {};

    //se ci sono delle dashboard
    if (Dashboard.dashboards_ul.length > 0) {
        //cicla le dashboards rilevate
        $.each(Dashboard.dashboards_ul, function (indexDashboard, dashboard) {
            //oggetto della dashboard
            var dashboardObj = $(dashboard);

            //recupera tutti gli elementi
            var elements = dashboardObj.find('li');

            //array con tutti gli elementi di questa sezione
            var dashboardElements = new Array();

            //per ciascuno, recupera l'ordine
            $.each(elements, function (indexElement, element) {
                var code = $(element).attr('data-code');
                var module_name = $(element).attr('data-module-name');

                var objectname = {code: code, module_name: module_name};

                dashboardElements.push(objectname);
            });

            //salva i dati
            array_return[indexDashboard] = dashboardElements;
        });
    }

    return array_return;
};
Dashboard.restoreMatrixDashboards = function () {
    //se è compilata la variabile
    if (Dashboard.dashboard_matrix) {
        //se ci sono delle dashboard
        if (Dashboard.dashboards_ul.length > 0) {
            //cicla le dashboards rilevate
            $.each(Dashboard.dashboards_ul, function (indexDashboard, dashboard) {
                //oggetto dashboard
                var dashboardObj = $(dashboard);

                //recupera la matrice della dashboard, di questa sezione
                var matrix = Dashboard.dashboard_matrix[indexDashboard];

                //se l'ha trovata
                if (matrix) {
                    //id della dashboard in esame
                    var id_dashboard = dashboardObj.attr('id');

                    //recupera il container temporaneo
                    var dashboardObjTemp = dashboardObj.parent().find('#' + id_dashboard + '-temp');

                    //copia tutti gli elementi nel container temporaneo
                    dashboardObjTemp.html(dashboardObj.html());

                    //svuota il container normale
                    dashboardObj.empty();

                    //cicla la matrice della sezione della dashboard
                    $.each(matrix, function (index, object_data) {
                        var code = object_data.code;
                        var module_name = object_data.module_name;

                        var element = dashboardObjTemp.find('li[data-code="' + code + '"]').first();

                        //posiziona l'elemento
                        dashboardObj.append(element);
                    });

                    //svuota il container temporaneo
                    dashboardObjTemp.empty();
                }
            });
        }
    }
};

var Introduzione = {};
var IntroduzioneDashboard = {};

Introduzione.tour_var = null;
Introduzione.cookie_name = "introduzioni";
Introduzione.init = function (steps) {
    Introduzione.tour_var = new Tour({
        orphan: true,
        onShown: function (tour) {
            var tourObj = $(".tour-tour");
            var navigation = tourObj.find(".popover-navigation");

            navigation.find("[data-role='prev']").html("&laquo; Indietro");
            navigation.find("[data-role='next']").html("Avanti &raquo;");
            navigation.find("[data-role='end']").html("Termina");
        },
        steps: steps
    }).init();
};
Introduzione.show = function () {
    Introduzione.tour_var.goTo(0);
    Introduzione.tour_var.restart();
};
Introduzione.setIntroShow = function (tipo_introduzione) {
    var cookie_val = Cookie.getCookie(Introduzione.cookie_name);
    if (!cookie_val) {
        cookie_val = {};
    }
    else {
        cookie_val = JSON.parse(cookie_val);
    }

    cookie_val[tipo_introduzione] = true;

    Cookie.setCookie(Introduzione.cookie_name, JSON.stringify(cookie_val), 365 * 10);
};
Introduzione.isIntroShow = function (tipo_introduzione) {
    var cookie_val = Cookie.getCookie(Introduzione.cookie_name);

    if (!cookie_val) {
        return false;
    }
    else {
        cookie_val = JSON.parse(cookie_val);

        return (cookie_val[tipo_introduzione] && (cookie_val[tipo_introduzione] == true || cookie_val[tipo_introduzione] == "true") ? true : false);
    }
};

var Cookie = {};
Cookie.setCookie = function (cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
};
Cookie.getCookie = function (cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ')
            c = c.substring(1);
        if (c.indexOf(name) == 0)
            return c.substring(name.length, c.length);
    }
    return "";
};

$(document).ready(function () {
    if (top.location != location) {
        top.location.href = document.location.href;
    }
    $(function () {
        window.prettyPrint && prettyPrint();
        $('.nav-tabs:first').tabdrop();
        $('.nav-tabs:last').tabdrop({text: 'More options'});
        $('.nav-pills').tabdrop({text: 'With pills'});
    });
});