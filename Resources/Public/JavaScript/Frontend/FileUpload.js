
function multiuploadNextAll(element, selector) {
  let siblings = [];
  while (element = element.nextElementSibling) {
    if (element.matches(selector)) {
      siblings.push(element);
    }
  }
  return siblings;
}

function addListenerToMultiUploadDeleteButton(deleteBtn,dt,inputField) {
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
      addListenerToMultiUploadDeleteButton(n,dt,element);
      fileBloc.appendChild(n);
      fileBloc.appendChild(fileName);
      multiuploadNextAll(element, '.multiupload-files')[0].appendChild(fileBloc);
    }
    for (let file of element.files) {
      dt.items.add(file);
    }
    element.files = dt.files;
  });

  multiuploadNextAll(element, '.multiupload-files')[0].querySelectorAll('.multiupload-file-delete').forEach(function (deleteBtn) {
    addListenerToMultiUploadDeleteButton(deleteBtn,dt,element);
  });
});