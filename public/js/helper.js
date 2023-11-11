// Function to create alert box
const dismiss_alert = (type, msg, input) => {
  if (input == 'html') {
    let html = `
        <div class="alert ${type} alert-dismissible fade show" role="alert">
            ${msg}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        `;

    return html;
  } else if (input == 'node') {
    let mainDiv = document.createElement('div');
    mainDiv.className = `alert ${type} alert-dismissible fade show`;
    mainDiv.role = 'alert';
    mainDiv.innerHTML = `
            ${msg}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>`;
    return mainDiv;
  }
};

// Function to create button loaders and enabled disable them
const btnLoaderAction = (el, displayFirst, displaySecond, disabled) => {
  document.getElementById(el).children[0].style.display = displayFirst;
  document.getElementById(el).children[1].style.display = displaySecond;
  document.getElementById(el).disabled = disabled;
};

// Function to load and remove spinner with appropriate text 
const spinnerLoaderAction = (display, text) => {
  document.getElementById('overlay').style.display = display;
  document.getElementById('loading_val').innerHTML = text || 'Loading...';
}
// End

// Function to clear the fields of given forms (by array of ids)
const clearForm = (formidarr, clearFields, clearError) => {
  formidarr.forEach((formid) => {
      let form = document.getElementById(formid);
      if(form) {
          let formfieldlst = form.querySelectorAll('.clearFields');
          if(clearFields) {
              formfieldlst.forEach((el) => {
                  if(el.type === 'checkbox') {
                    el.checked = false;
                  } else {
                    el.value = '';
                  }
              });
          }
          
          if(clearError) {
              formfieldlst.forEach((el) => {
                  if(el.classList.contains('is-invalid')) {
                      el.classList.remove('is-invalid');
                  }
                  
                  while(el.nextElementSibling) {
                      el.nextElementSibling.remove();
                  }
              });
          }
      }
  });
}
