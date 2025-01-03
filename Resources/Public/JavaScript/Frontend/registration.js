

document.addEventListener('DOMContentLoaded', function() {
  const resendOptInButton = document.querySelector('.btn-resend-opt-in');

  if (resendOptInButton) {
    const uri = resendOptInButton.getAttribute('data-uri');

    resendOptInButton.addEventListener('click', function(event) {
      event.preventDefault();

      fetch(uri, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json'
        }
      })
        .then(response => response.json())
        .then(data => {

          if (data.success) {

          }
          if (!data.success && data.wait) {

          }

          console.log(data);
          // Handle the JSON response data here
        })
        .catch(error => {
          console.error('Error:', error);
        });
    });
  }
});
