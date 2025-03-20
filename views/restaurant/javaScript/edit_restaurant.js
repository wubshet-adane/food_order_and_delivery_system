function edit() {
    const input_tags = document.querySelectorAll('input, textarea, select');
    const enable_editting = document.getElementById('boEdit');  // Get the edit image text
    const update_btn = document.getElementById('boUpdate');  // Get the update button
    const info = document.querySelectorAll('span'); //get restaurant data in span tag


    // Initial setup: disable input field, hide the update button, set button text to 'edit'
    info.forEach(element => {
        element.style.display = '';
    });
    input_tags.forEach(input_tag => {
        input_tag.style.display = 'none';  //hide elements
    });
    update_btn.style.display = 'none';  // Hide the update button

    // Add event listener for the edit button
    enable_editting.addEventListener('click', function() {
       input_tags.forEach(input_tag => {
            if (input_tag.style.display == 'none') {
                input_tag.style.display = 'block';  //display elements
                info.forEach(element => {
                    element.style.display = 'none';
                });
                update_btn.style.display = 'block';// Show the update button after editing starts
                enable_editting.textContent = 'Cancel';  // Change the edit button text to cancel
                enable_editting.style.backgroundColor = '#19f';// change background color
                enable_editting.style.color = 'red';  // Change the edit button color back to default
           }
            else {
                input_tag.style.display = 'none';  // Hide the update button
                info.forEach(element => {
                    element.style.display = '';
                });
                update_btn.style.display = 'none';  // Hide the update button after updating
                enable_editting.textContent = 'Edit Restaurant';  // Change the edit button text back to edit
                enable_editting.style.backgroundColor = '#03a000';// change background color
                enable_editting.style.color = '#fff';  // Change the edit button color back to default
            }
        });
       
    });

    // Add event listener for the update button
    update_btn.addEventListener('change' , function() {
        input_tags.forEach(input_tag => {
            input_tag.style.display = 'none';  // hide elements
        });
        enable_editting.textContent = 'Edit Restaurant';  // Change the edit button text back to edit
        enable_editting.style.color = '#fff';  // Change the edit button color back to default
        update_btn.style.display = 'none';  // Hide the update button after updating});
    });
}
// Call the edit function when the page loads or when needed
edit();