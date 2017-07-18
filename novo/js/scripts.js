//FULLPAGE

$(document).ready(function() {
	$('#fullpage').fullpage({
        menu: '#menu',
        onLeave: function(index, nextIndex, direction){
            var leavingSection = $(this);

            if(index == 2 && direction =='up'){
                $('#top').fadeOut('slow');
            }	

            else if(direction == 'down'){
                $('#top').fadeIn('slow');
            }
        },
//        parallax: true,
//        parallaxOptions: {type: 'reveal', percentage: 62, property: 'translate'},
	});
});

//THUMBS QUADRADAS - PORTFOLIO

$(document).ready(function() {
    var cw = $('.etka-card-image').width();
    $('.etka-card-image').css({'height':cw+'px'});
});

//CONTROLE DE SOM DA HOME
$(document).ready(function() {
    $("#mute-video").click( function (){
        if( $("video").prop('muted') ) {
            $("video").prop('muted', false);
        } else {
            $("video").prop('muted', true);
        }
    });
    $('.etka_som-btn').click(function(e) {
        e.preventDefault();
        $(this).toggleClass('etka_mute').siblings().removeClass('etka_unmute');
    });
    $('.etka_som-btn').click(function(e) {
        e.preventDefault();
        $(this).toggleClass('etka_unmute').siblings().removeClass('etka_mute');
    });
});

// >> DIALOGS <<	 

//ORÇAMENTO
//orçamento-rótulos
$(document).ready(function() {
    'use strict';
    var button = document.querySelector('#show-dialog-orcamento-rotulos');
    var dialog = document.querySelector('#dialog-orcamento-rotulos');
    dialogPolyfill.registerDialog(dialog);
    // Now dialog acts like a native <dialog>.
    button.addEventListener('click', function() {
    dialog.showModal();
    });
    dialog.querySelector('.close').addEventListener('click', function () {
        dialog.close();
    });
});

//orçamento-lacres
$(document).ready(function() {
    'use strict';
    var button = document.querySelector('#show-dialog-orcamento-lacres');
    var dialog = document.querySelector('#dialog-orcamento-lacres');
    dialogPolyfill.registerDialog(dialog);
    // Now dialog acts like a native <dialog>.
    button.addEventListener('click', function() {
    dialog.showModal();
    });
    dialog.querySelector('.close').addEventListener('click', function () {
        dialog.close();
    });
});

//orçamento-ingressos
$(document).ready(function() {
    'use strict';
    var button = document.querySelector('#show-dialog-orcamento-ingressos');
    var dialog = document.querySelector('#dialog-orcamento-ingressos');
    dialogPolyfill.registerDialog(dialog);
    // Now dialog acts like a native <dialog>.
    button.addEventListener('click', function() {
    dialog.showModal();
    });
    dialog.querySelector('.close').addEventListener('click', function () {
        dialog.close();
    });
});

//orçamento-validade
$(document).ready(function() {
    'use strict';
    var button = document.querySelector('#show-dialog-orcamento-validade');
    var dialog = document.querySelector('#dialog-orcamento-validade');
    dialogPolyfill.registerDialog(dialog);
    // Now dialog acts like a native <dialog>.
    button.addEventListener('click', function() {
    dialog.showModal();
    });
    dialog.querySelector('.close').addEventListener('click', function () {
        dialog.close();
    });
});

//FAZEMOS
//fazemos-flexo
$(document).ready(function() {
    'use strict';
    var button = document.querySelector('#show-dialog-flexo');
    var dialog = document.querySelector('#dialog-flexo');
    dialogPolyfill.registerDialog(dialog);
    // Now dialog acts like a native <dialog>.
    button.addEventListener('click', function() {
    dialog.showModal();
    });
    dialog.querySelector('a.close').addEventListener('click', function () {
        dialog.close();
    });
    dialog.querySelector('button.close').addEventListener('click', function () {
        dialog.close();
    });
});

//fazemos-digital
$(document).ready(function() {
    'use strict';
    var button = document.querySelector('#show-dialog-digital');
    var dialog = document.querySelector('#dialog-digital');
    dialogPolyfill.registerDialog(dialog);
    // Now dialog acts like a native <dialog>.
    button.addEventListener('click', function() {
    dialog.showModal();
    });
    dialog.querySelector('a.close').addEventListener('click', function () {
        dialog.close();
    });
    dialog.querySelector('button.close').addEventListener('click', function () {
        dialog.close();
    });
});

//fazemos-offset
$(document).ready(function() {
    'use strict';
    var button = document.querySelector('#show-dialog-offset');
    var dialog = document.querySelector('#dialog-offset');
    dialogPolyfill.registerDialog(dialog);
    // Now dialog acts like a native <dialog>.
    button.addEventListener('click', function() {
    dialog.showModal();
    });
    dialog.querySelector('a.close').addEventListener('click', function () {
        dialog.close();
    });
    dialog.querySelector('button.close').addEventListener('click', function () {
        dialog.close();
    });
});

//fazemos-consultoria
$(document).ready(function() {
    'use strict';
    var button = document.querySelector('#show-dialog-consultoria');
    var dialog = document.querySelector('#dialog-consultoria');
    dialogPolyfill.registerDialog(dialog);
    // Now dialog acts like a native <dialog>.
    button.addEventListener('click', function() {
    dialog.showModal();
    });
    dialog.querySelector('a.close').addEventListener('click', function () {
        dialog.close();
    });
    dialog.querySelector('button.close').addEventListener('click', function () {
        dialog.close();
    });
});

//PREÇO

$('#slide_01').on('input',function(){
   $("#text_01").get(0).MaterialTextfield.change(this.value);
});
$('#inp_text_01').keyup(function() {
   $("#slide_01").get(0).MaterialSlider.change($('#inp_text_01').val());
  console.dir($('#slide_01'));
});

//function showMessage(value){
//    document.getElementById("slidervalue-etka_preco").innerHTML = value;
//}
// referencias: 
//https://stackoverflow.com/questions/34029483/creating-a-slider-with-editable-number-field-in-material-design-lite
//https://www.tutorialspoint.com/materialdesignlite/materialdesignlite_sliders.htm

//lacre
$(document).ready(function() {
    'use strict';
    var button = document.querySelector('#show-dialog-preco-lacre');
    var dialog = document.querySelector('#dialog-preco-lacre');
    dialogPolyfill.registerDialog(dialog);
    // Now dialog acts like a native <dialog>.
    button.addEventListener('click', function() {
    dialog.showModal();
    });
    dialog.querySelector('.close').addEventListener('click', function () {
        dialog.close();
    });
});

//garantia
$(document).ready(function() {
    'use strict';
    var button = document.querySelector('#show-dialog-preco-garantia');
    var dialog = document.querySelector('#dialog-preco-garantia');
    dialogPolyfill.registerDialog(dialog);
    // Now dialog acts like a native <dialog>.
    button.addEventListener('click', function() {
    dialog.showModal();
    });
    dialog.querySelector('.close').addEventListener('click', function () {
        dialog.close();
    });
});

//ingresso
$(document).ready(function() {
    'use strict';
    var button = document.querySelector('#show-dialog-preco-ingresso');
    var dialog = document.querySelector('#dialog-preco-ingresso');
    dialogPolyfill.registerDialog(dialog);
    // Now dialog acts like a native <dialog>.
    button.addEventListener('click', function() {
    dialog.showModal();
    });
    dialog.querySelector('.close').addEventListener('click', function () {
        dialog.close();
    });
});

//validade
$(document).ready(function() {
    'use strict';
    var button = document.querySelector('#show-dialog-preco-validade');
    var dialog = document.querySelector('#dialog-preco-validade');
    dialogPolyfill.registerDialog(dialog);
    // Now dialog acts like a native <dialog>.
    button.addEventListener('click', function() {
    dialog.showModal();
    });
    dialog.querySelector('.close').addEventListener('click', function () {
        dialog.close();
    });
});

//PORTFOLIO
//modelo
//$(document).ready(function() {
//    'use strict';
//    var button = document.querySelector('#show-dialog-port_modelo');
//    var dialog = document.querySelector('#dialog-port_modelo');
//    dialogPolyfill.registerDialog(dialog);
//    // Now dialog acts like a native <dialog>.
//    button.addEventListener('click', function() {
//    dialog.showModal();
//    });
//    dialog.querySelector('.close').addEventListener('click', function () {
//        dialog.close();
//    });
//});