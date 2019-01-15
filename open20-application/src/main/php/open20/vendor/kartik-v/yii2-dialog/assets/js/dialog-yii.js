/*!
 * @package   yii2-dialog
 * @version   1.0.3
 *
 * Override Yii confirmation dialog with Krajee Dialog.
 *
 * Author: Kartik Visweswaran
 * Copyleft: 2015, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
var krajeeYiiConfirm;
(function () {
    "use strict";
    krajeeYiiConfirm = function(dialog) {
        dialog = dialog || 'krajeeDialog';
        var krajeeDialog = window[dialog] || '';
        if (!krajeeDialog) {
            return;
        }
        yii.confirm = function (message, ok, cancel) {
            krajeeDialog.confirm(message, function(result) {
                if (result) {
                    !ok || ok();
                } else {
                    !cancel || cancel();
                }
            });
        };
    };
})();