var Correlazioni = {};
Correlazioni.gestisciSelezione = function (element, postName, postKey, singleSelection) {
    if ((singleSelection === undefined) || (singleSelection === null)) {
        singleSelection = false;
    }

    //recupera il contenitore degli input hidden
    var hiddenInputContainer = $(".hiddenInputContainer");

    //recupera l'oggetto selezionato
    var obj = $(element);

    //id dell'elemento selezionato
    var id = obj.val();

    //se l'oggetto è selezionato, lo aggiunge
    if (obj.is(":checked")) {
        //crea l'input hidden
        var newHiddenInput = "<input type='hidden' name='" + postName + "[" + postKey + "][]' value='" + id + "'/>";

        if (singleSelection) {
            obj.parents('tr').siblings().find('input.m2m-target-checkbox:checked').trigger('click');
            hiddenInputContainer.empty();
        }

        //lo inserisce nel contenitore
        hiddenInputContainer.append(newHiddenInput);

    }
    //altrimenti lo rimuove
    else {
        //cerca l'input hidden
        var hiddenInput = hiddenInputContainer.find('[value="' + id + '"]');
        if (hiddenInput.length > 0) {
            hiddenInput.remove();
        }
    }
};
