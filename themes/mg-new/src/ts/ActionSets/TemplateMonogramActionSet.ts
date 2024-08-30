import { ActionSet }                        from "../Base/ActionSet";

export class TemplateMonogramActionSet extends ActionSet {
    constructor() {
        super();
    }

    load(): void {
        jQuery(<any>document).on('keyup', 'li.gfield.letter input', function (e) {
            let theParent = jQuery(this).closest('li.gfield.letter');

            if (e.keyCode == 8) {
                if ((<any>this).value.length == 0) {
                    jQuery(theParent).prev('li').find('input').focus();
                }
            } else {
                if ((<any>this).value.length == (<any>this).maxLength) {
                    jQuery(theParent).next('li').find('input').focus();
                }
            }
        });

        let persProductForm = jQuery('#gf_20');

        if (persProductForm.length) {

            // Update the state selected for tax based off of the select in the address

            jQuery("#input_20_9_4").on("change", function () {
                let selectedState = jQuery("#input_20_9_4 option:selected").text();

                jQuery('#input_20_14 option').filter(function () {
                    return (jQuery(this).text() == selectedState);
                }).prop('selected', true);

                let taxSelect = jQuery('#input_20_14');

                let selection = (<string>taxSelect.val());
                let selecSplit = selection.split("|");
                let tax = selecSplit[1];
                let newTax = parseFloat(tax);

                taxSelect.change();

                if (selecSplit[1] === "0") {
                    let tax = " $0.00";
                } else {
                    let tax = "$" + selecSplit[1];
                }

                jQuery('.tax-value-pers').text(tax);
                jQuery('#input_20_15').val(tax);

            });

        }
    }
}