function toggleRecoverPasswordForm() {
  document.querySelector('#LoginForm').classList.toggle('hide')
  document.querySelector('#RecoverPasswordForm').classList.toggle('hide')
}

function triggerEventNative (el, eventName) {
  var event = document.createEvent('HTMLEvents')
  event.initEvent(eventName, true, false)
  el.dispatchEvent(event)
}

function updateFormSelect(el, options) {
  let html = ''
  for (const option of options) {
    html += `<option value="${option.id}">${option.description}</option>`
  }
  el.innerHTML = html

  M.FormSelect.init(el)
}

function confirmDeleteSubmit(el, event, text = '') {
  event.preventDefault()
  swal({
    title: "Are you sure?",
    text: text,
    icon: "warning",
    buttons: true,
    dangerMode: true,
  })
  .then((willDelete) => {
    if (willDelete) {
      swal("It has been deleted!", {
        icon: "success",
      })
      setTimeout(() => {
        el.submit()
      }, 1000);
    }
    })
}


M.AutoInit();
