$(document).ready(function(){
  refreshRipple();
});

function refreshRipple(){
  $('.ripple-container').on('mousedown', function(event){
    var buttonWidth = $(this).width(),
    buttonHeight = $(this).height();
    var radius = Math.max(buttonWidth,buttonHeight);
    var x = event.pageX - $(this).offset().left - radius/2 ,
    y = event.pageY - $(this).offset().top - radius/2;
    $('.ripple').remove();
    $(this).prepend("<span class='ripple'></span>");
    $(".ripple").css({
      width: radius + 'px',
      height: radius + 'px',
      top: y + 'px',
      left: x + 'px'
    }).addClass("rippleAnim");
  });
}
