function multiuploadNextAll(element, selector) {
  let siblings = [];
  while (element = element.nextElementSibling) {
    if (element.matches(selector)) {
      siblings.push(element);
    }
  }
  return siblings;
}

function addListenerToMultiUploadDeleteButton(deleteBtn, dt, inputField) {
  deleteBtn.addEventListener('click', function () {
    let resource = this.parentNode.getAttribute('data-target');
    if (resource) {
      document.querySelector(resource).remove();
    }
    let name = this.nextElementSibling.innerText;
    this.parentNode.remove();
    for (let i = 0; i < dt.items.length; i++) {
      if (name === dt.items[i].getAsFile().name) {
        dt.items.remove(i);
      }
    }
    inputField.files = dt.files;
    updateFieldState(inputField);
  });
}

document.querySelectorAll('input[multiple]').forEach(function (element) {
  const dt = new DataTransfer();

  element.addEventListener('change', function (e) {

    for (let i = 0; i < element.files.length; i++) {
      let fileBloc = document.createElement('span');
      fileBloc.classList.add('multiupload-file-block');
      let fileName = document.createElement('span');
      fileName.classList.add('name');
      fileName.innerText = this.files.item(i).name;
      let n = document.createElement('span');
      n.classList.add('multiupload-file-delete');
      let m = document.createElement('span');
      m.innerHTML = '+';
      n.appendChild(m);
      addListenerToMultiUploadDeleteButton(n, dt, element);
      fileBloc.appendChild(n);
      fileBloc.appendChild(fileName);
      multiuploadNextAll(element, '.multiupload-files')[0].appendChild(fileBloc);
    }
    for (let file of element.files) {
      dt.items.add(file);
    }
    element.files = dt.files;

    updateFieldState(element);
  });

  multiuploadNextAll(element, '.multiupload-files')[0].querySelectorAll('.multiupload-file-delete').forEach(function (deleteBtn) {
    addListenerToMultiUploadDeleteButton(deleteBtn, dt, element);
  });
});

function updateFieldState(element) {

  // check file size
  let maxSize = element.getAttribute('data-max-filesize');

  let sum = 0;
  for (let i = 0; i < element.files.length; i++) {
    sum += element.files.item(i).size;
  }

  element.classList.remove('is-invalid');
  let errorDiv = element.parentElement.querySelector('.invalid-feedback');
  if (errorDiv) {
    errorDiv.remove();
  }

  if (sum > maxSize) {
    element.classList.add('is-invalid');
    let errorDiv = element.parentElement.appendChild(document.createElement('div'));
    errorDiv.classList.add('invalid-feedback');
    errorDiv.innerHTML = element.getAttribute('data-msg-filesize-exceeded');
    let elements = element.form.elements;
    for (let i = 0; i < elements.length; i++) {
      if (elements[i].type === 'submit') {
        elements[i].disabled = true;
      }
    }
  } else {
    let elements = element.form.elements;
    for (let i = 0; i < elements.length; i++) {
      if (elements[i].type === 'submit') {
        elements[i].disabled = false;
      }
    }
  }

}
