$(function(){

  var faq = $('.faqs__define'),
    btn = faq.find('.open');

  btn.on('click', function(e){
    // faq.removeClass('is-open');
    $(this).parent().parent().addClass('is-open');
  })
})