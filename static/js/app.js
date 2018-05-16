function toggleRecoverPasswordForm() {
  document.querySelector('#LoginForm').classList.toggle('hide')
  document.querySelector('#RecoverPasswordForm').classList.toggle('hide')
}

function triggerEventNative (el, eventName) {
  var event = document.createEvent('HTMLEvents');
  event.initEvent(eventName, true, false);
  el.dispatchEvent(event);
}


M.AutoInit();
