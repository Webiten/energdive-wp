jQuery(function($){
  const ajaxUrl = ENERG.ajax;
  const nonce = $('#energ_nonce').val();

  $('#btn_send_email_otp').click(() => {
    $.post(ajaxUrl,{
      action:'energ_send_email_otp',
      nonce,
      email:$('#cr_email').val(),
      phone:$('#cr_phone').val()
    },res=>{
      if(res.success){
        $('#email_otp_box').show();
        $('#step1_msg').css('color','green').text('OTP sent');
      } else {
        $('#step1_msg').text(res.data);
      }
    });
  });

  $('#btn_verify_email_otp').click(()=>{
    $.post(ajaxUrl,{
      action:'energ_verify_email_otp',
      nonce,
      email:$('#cr_email').val(),
      otp:$('#cr_email_otp').val(),
      phone:$('#cr_phone').val()
    },res=>{
      if(res.success){
        $('#cr_email_otp_verified').val('1');
        $('#step_email').hide();
        $('#step_full_form').show();
        $('#cr_email_final').val($('#cr_email').val());
      } else {
        $('#step1_msg').text(res.data);
      }
    });
  });

  $('#btn_submit_final').click(()=>{
    $.post(ajaxUrl,{
      action:'energ_complete_registration',
      nonce,
      phone:$('#cr_phone').val(),
      email:$('#cr_email_final').val(),
      first_name:$('#cr_first').val(),
      last_name:$('#cr_last').val(),
      organization:$('#cr_org').val(),
      designation:$('#cr_designation').val(),
      country:$('#cr_country').val()
    },res=>{
      if(res.success){
        window.location.href = res.data.redirect;
      } else {
        $('#final_msg').text(res.data);
      }
    });
  });
});
