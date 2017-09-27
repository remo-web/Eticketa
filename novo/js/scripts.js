//FULLPAGE
$(document).ready(function() {
        if($(window).width() < 960) {
            //mobile
        }
            else{
                
                
                //QUANDO O MOBILE ESTIVE DIREITO É PARA TIRAR O QUE ESTÁ AQUI PRA CIMA E DEIXAR SÓ ATÉ...
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
                
                //AQUI!
                
                
            }
        });

//--------------------------------------//

//THUMBS QUADRADAS - PORTFOLIO

$(document).ready(function() {
    var cw = $('.etka-card-image').width();
    $('.etka-card-image').css({'height':cw+'px'});
});

//--------------------------------------//

//CONTROLE DE SOM DA HOME

//não toca no mobile
$(document).ready(function() {
    var vid = document.getElementById("etka_home-video");
    var pause = document.createAttribute("paused");
    var play = document.createAttribute("data-autoplay");
    $("#etka_home-video").ready(function(){
        if($(window).width() < 960) {
            vid.setAttributeNode(pause);
            return false;
        } else {
            vid.setAttributeNode(play);
            return false;
        }
    })
});

//botão
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

//--------------------------------------//

//DIALOGS	 

//Orçamento
//rótulos
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

//lacres
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

//ingressos
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

//validade
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

//Fazemos
//flexo
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

//digital
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

//offset
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

//consultoria
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

//Preço
//delivery
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

//--------------------------------------//

//SLIDER
//delivery
$(document).ready(function() {
    $('#etka_preco-delivery_slider').on('input',function(){
        $("#etka_preco-delivery_slider-label").get(0).MaterialTextfield.change(this.value);
    });
    $('#etka_preco-delivery_slider-label_input').keyup(function() {
        $("#etka_preco-delivery_slider").get(0).MaterialSlider.change($('#etka_preco-delivery_slider-label_input').val());
        console.dir($('#etka_preco-delivery_slider'));
    });
});

//garantia
$(document).ready(function() {
    $('#etka_preco-garantia_slider').on('input',function(){
        $("#etka_preco-garantia_slider-label").get(0).MaterialTextfield.change(this.value);
    });
    $('#etka_preco-garantia_slider-label_input').keyup(function() {
        $("#etka_preco-garantia_slider").get(0).MaterialSlider.change($('#etka_preco-garantia_slider-label_input').val());
        console.dir($('#etka_preco-garantia_slider'));
    });
});

//ingresso
$(document).ready(function() {
    $('#etka_preco-ingresso_slider').on('input',function(){
        $("#etka_preco-ingresso_slider-label").get(0).MaterialTextfield.change(this.value);
    });
    $('#etka_preco-ingresso_slider-label_input').keyup(function() {
        $("#etka_preco-ingresso_slider").get(0).MaterialSlider.change($('#etka_preco-ingresso_slider-label_input').val());
        console.dir($('#etka_preco-ingresso_slider'));
    });
});

//validade
$(document).ready(function() {
    $('#etka_preco-validade_slider').on('input',function(){
        $("#etka_preco-validade_slider-label").get(0).MaterialTextfield.change(this.value);
    });
    $('#etka_preco-validade_slider-label_input').keyup(function() {
        $("#etka_preco-validade_slider").get(0).MaterialSlider.change($('#etka_preco-validade_slider-label_input').val());
        console.dir($('#etka_preco-validade_slider'));
    });
});

//--------------------------------------//

//BOTÃO ANEXAR || Gabi

//Orçamento
//rótulos
$(document).ready(function() {
    $('#o_rotulos-anexo').on('change',function(){
        var numArquivos = $(this).get(0).files.length;
        if ( numArquivos > 1 ) {
	        $('#etka_rotulos-texto').val( numArquivos+' arquivos' );
        } else {
	        $('#etka_rotulos-texto').val( $(this).val() );
        }
    });
});

//lacres
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

 //ingressos
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

//validade
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

//Preço
//delivery
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

//ingressos
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

//--------------------------------------//


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