window.onload = function () {
  let file
  document.getElementById('download').addEventListener('click', function () {
    document.getElementById('fileLoad').click()
  })

  document.getElementById('fileLoad').addEventListener('change', function(event) {
    const file = event.target.files[0]
    const reader = new FileReader()
    reader.onload = e => {
      console.log(reader.result)
    }
    reader.readAsDataURL(file)
  })

}