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
        scrollOverflow: true,
        normalScrollElements: 'dialog'
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

    //botao anexo rotulos - Gabi
    $(document).ready(function() {
    $('#etka_rotulos-anexo').on('change',function(){
        var numArquivos = $(this).get(0).files.length;
        if ( numArquivos > 1 ) {
	        $('#etka_rotulos-texto').val( numArquivos+' arquivos' );
        } else {
	        $('#etka_rotulos-texto').val( $(this).val() );
        }
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

    //botao anexo lacres - Gabi
    $(document).ready(function() {
    $('#etka_lacres-anexo').on('change',function(){
        var numArquivos = $(this).get(0).files.length;
        if ( numArquivos > 1 ) {
	        $('#etka_lacres-texto').val( numArquivos+' arquivos' );
        } else {
	        $('#etka_lacres-texto').val( $(this).val() );
        }
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

    //botao anexo controle de acesso - Gabi
    $(document).ready(function() {
    $('#etka_acesso-anexo').on('change',function(){
        var numArquivos = $(this).get(0).files.length;
        if ( numArquivos > 1 ) {
	        $('#etka_acesso-texto').val( numArquivos+' arquivos' );
        } else {
	        $('#etka_acesso-texto').val( $(this).val() );
        }
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

    //botao anexo etiquetas de validade - Gabi
    $(document).ready(function() {
    $('#etka_etiqueta-anexo').on('change',function(){
        var numArquivos = $(this).get(0).files.length;
        if ( numArquivos > 1 ) {
	        $('#etka_etiqueta-texto').val( numArquivos+' arquivos' );
        } else {
	        $('#etka_etiqueta-texto').val( $(this).val() );
        }
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

//delivery
    //dialog
$(document).ready(function() {
    'use strict';
    var button = document.querySelector('#show-dialog-preco-delivery');
    var dialog = document.querySelector('#dialog-preco-delivery');
    dialogPolyfill.registerDialog(dialog);
    // Now dialog acts like a native <dialog>.
    button.addEventListener('click', function() {
    dialog.showModal();
    });
    dialog.querySelector('.close').addEventListener('click', function () {
        dialog.close();
    });
});
    //slider
$(document).ready(function() {
    $('#etka_preco-delivery_slider').on('input',function(){
        $("#etka_preco-delivery_slider-label").get(0).MaterialTextfield.change(this.value);
    });
    $('#etka_preco-delivery_slider-label_input').keyup(function() {
        $("#etka_preco-delivery_slider").get(0).MaterialSlider.change($('#etka_preco-delivery_slider-label_input').val());
        console.dir($('#etka_preco-delivery_slider'));
    });
});
    //anexo lacres para delivery - Preco | Gabi

$(document).ready(function() {
    $('#etka_delivery-anexo').on('change',function(){
        var numArquivos = $(this).get(0).files.length;
        if ( numArquivos > 1 ) {
	        $('#etka_delivery-texto').val( numArquivos+' arquivos' );
        } else {
	        $('#etka_delivery-texto').val( $(this).val() );
        }
    });
});


//garantia
    //dialog
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
    //slider
$(document).ready(function() {
    $('#etka_preco-garantia_slider').on('input',function(){
        $("#etka_preco-garantia_slider-label").get(0).MaterialTextfield.change(this.value);
    });
    $('#etka_preco-garantia_slider-label_input').keyup(function() {
        $("#etka_preco-garantia_slider").get(0).MaterialSlider.change($('#etka_preco-garantia_slider-label_input').val());
        console.dir($('#etka_preco-garantia_slider'));
    });
});

    // botao anexo lacres de garantia - Preco | Gabi
    $(document).ready(function() {
    $('#etka_garantia-anexo').on('change',function(){
        var numArquivos = $(this).get(0).files.length;
        if ( numArquivos > 1 ) {
	        $('#etka_garantia-texto').val( numArquivos+' arquivos' );
        } else {
	        $('#etka_garantia-texto').val( $(this).val() );
        }
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
    //slider
$(document).ready(function() {
    $('#etka_preco-ingresso_slider').on('input',function(){
        $("#etka_preco-ingresso_slider-label").get(0).MaterialTextfield.change(this.value);
    });
    $('#etka_preco-ingresso_slider-label_input').keyup(function() {
        $("#etka_preco-ingresso_slider").get(0).MaterialSlider.change($('#etka_preco-ingresso_slider-label_input').val());
        console.dir($('#etka_preco-ingresso_slider'));
    });
});

    // botao anexo tickets e ingressos - Preco | Gabi
    $(document).ready(function() {
    $('#etka_ingressos-anexo').on('change',function(){
        var numArquivos = $(this).get(0).files.length;
        if ( numArquivos > 1 ) {
	        $('#etka_ingressos-texto').val( numArquivos+' arquivos' );
        } else {
	        $('#etka_ingressos-texto').val( $(this).val() );
        }
    });
});

//validade
    //dialog
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
    //slider
$(document).ready(function() {
    $('#etka_preco-validade_slider').on('input',function(){
        $("#etka_preco-validade_slider-label").get(0).MaterialTextfield.change(this.value);
    });
    $('#etka_preco-validade_slider-label_input').keyup(function() {
        $("#etka_preco-validade_slider").get(0).MaterialSlider.change($('#etka_preco-validade_slider-label_input').val());
        console.dir($('#etka_preco-validade_slider'));
    });
});

    // botao anexo etiquetas de validade - Preco | Gabi
    $(document).ready(function() {
    $('#etka_validade-anexo').on('change',function(){
        var numArquivos = $(this).get(0).files.length;
        if ( numArquivos > 1 ) {
	        $('#etka_validade-texto').val( numArquivos+' arquivos' );
        } else {
	        $('#etka_validade-texto').val( $(this).val() );
        }
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