window.onload = function () {
  $('#download').on('click', function () { // event который вызывает событие клик на input file
    document.getElementById('fileLoad').click()
  })
  let country = new Set() // Множество содержащее все города
  $('#fileLoad').on('change', function (event) {
    $('#download').addClass('downloading') // показ loader'a
    const file = event.target.files[0]
    const reader = new FileReader() // при загрузке xml файла берем данные как текст, а после парсим их
    reader.readAsText(file);
    reader.onloadend = e => {
      parser = new DOMParser();
      xmlDoc = parser.parseFromString(e.target.result, "application/xml");
      mas = Array.from($(xmlDoc).find('S_ORG_COUNTRY')) // берем столбец country
      mas.forEach(v => {
        country.add(v.textContent)
      })
      $.ajax({
        url: "/server/index.php",
        type: 'POST',
        data: JSON.stringify(e.target.result),
        processData: false, // Не обрабатываем файлы 
        contentType: false,
        success: function (respond, textStatus, jqXHR) {

          if (typeof respond.error === 'undefined') {
            // Файлы успешно загружены, делаем что нибудь здесь
            console.log(respond)
            // выведем пути к загруженным файлам в блок '.ajax-respond'

            var files_path = respond.files;
            var html = '';
            $.each(files_path, function (key, val) {
              html += val + '<br>';
            })
            $('.ajax-respond').html(html);
          } else {
            console.log('ОШИБКИ ОТВЕТА сервера: ' + respond.error);
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.log('ОШИБКИ AJAX запроса: ' + textStatus);
        }
      });
      country.forEach((v) => { // Добавление элемента списка
        $('.list').append(`<li>${v}</li>`)
      })
      $('#download').removeClass('downloading')
      $('.container').addClass('done')
    }
  })
}