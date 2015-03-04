/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var suma = 0;

function sumar_cheques(suma) {

    $(document).on("click", ".checks", function (event) {
//        alert('dd');
        var id = $(this).attr("id");				// Obtener id del input clickeado
        var monto = getNumericAttr(this, 'monto', 'float');				// Obtener id del input clickeado
//        var val = $(this).attr("value");			// Obtener value del input clickeado

        if ($('#' + id).is(":checked")) {			// Si (obtener objeto por id) esta marcado
            suma += monto;		// Sumar el valor asignado en Value
        } else {						// caso contrario
            suma -= monto;		// restar
        }

        $("#resultado").html("<b>Total Cheques: </b> " + suma.toFixed(2));	// Actualizar el div #resultado con la suma de los valores

//   return false; 
    });
    suma = 0;

//    $("#resultado").html("Total = " + suma.toFixed(2));
}


$(function () {
    sumar_cheques(suma);


});