

$("input[multiple]").each(function () {
  let element = this;
  const dt = new DataTransfer();

  $(element).on('change', function (e) {
    for (let i = 0; i < element.files.length; i++) {
      let fileBloc = $('<span/>', {class: 'multiupload-file-block'}),
        fileName = $('<span/>', {class: 'name', text: this.files.item(i).name});
      fileBloc.append('<span class="multiupload-file-delete"><span>+</span></span>')
        .append(fileName);
      $(element).nextAll('.multiupload-files').append(fileBloc);
    }
    for (let file of element.files) {
      dt.items.add(file);
    }
    element.files = dt.files;

  });

  $('.multiupload-file-delete',$(element).nextAll('.multiupload-files')).click(function () {
    let resource = $(this).parent().attr('data-target');
    if (resource) {
      $(resource).remove();
    }
    let name = $(this).next('span.name').text();
    $(this).parent().remove();
    for (let i = 0; i < dt.items.length; i++) {
      if (name === dt.items[i].getAsFile().name) {
        dt.items.remove(i);
      }
    }
    element.files = dt.files;
  });

});



