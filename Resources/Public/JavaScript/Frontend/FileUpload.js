// DataTransfer allows to manipulate the files in the input file
const dt = new DataTransfer();

window.addEventListener('DOMContentLoaded', () => {

  const showUploadedFiles = function(files, inputField) {
    for (var i = 0; i < files.length; i++) {
      let fileBlock = document.createElement('span');
      fileBlock.classList.add('file-block');

      let fileName = document.createElement('span');
      fileName.classList.add('name');
      fileName.innerText = files.item(i).name;

      let fileDelete = document.createElement('span');
      fileDelete.classList.add('file-delete');
      fileDelete.innerHTML = '&#10005';

      fileBlock.append(fileDelete);
      fileBlock.append(fileName);

      let filesNames = inputField.parentElement
          .getElementsByClassName('files-area')[0]
          .getElementsByClassName('filesList')[0]
          .getElementsByClassName('files-names')[0]
      filesNames.append(fileBlock);
    }
  }

  let multipleUploadInputFields = Array.from(document.querySelectorAll("input[multiple]"));

  // Initialize after page reload
  multipleUploadInputFields.forEach(inputField => showUploadedFiles(inputField.files, inputField));

  // Add files as single items when added via input field
  multipleUploadInputFields.forEach(inputField => inputField.addEventListener('change', function (e) {

    let inputField = e.target;

    showUploadedFiles(this.files, inputField)

    // Add files to dt
    for (let file of this.files) {
      dt.items.add(file);
    }
    this.files = dt.files;

    // Remove files from the list when user clicks on delete icon
    Array.from(document.querySelectorAll("span.file-delete")).forEach(e => e.addEventListener('click', function (e) {
      let name = e.target.nextElementSibling.innerHTML;
      e.target.parentElement.remove();
      for (let i = 0; i < dt.items.length; i++) {
        if (name === dt.items[i].getAsFile().name) {
          dt.items.remove(i);
        }
      }
      this.files = dt.files;
    }));
  }))
})