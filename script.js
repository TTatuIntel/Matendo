document.getElementById('application-form').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = {
        role: document.getElementById('role').value,
        fullName: document.getElementById('fullName').value,
        email: document.getElementById('email').value,
        password: document.getElementById('password').value,
        confirmPassword: document.getElementById('confirmPassword').value
    };
    console.log('Form submitted', formData);
    // Here you would typically send the data to your server
    alert('Application submitted successfully!');
});

const switchers = [...document.querySelectorAll('.switcher')]

switchers.forEach(item => {
	item.addEventListener('click', function() {
		switchers.forEach(item => item.parentElement.classList.remove('is-active'))
		this.parentElement.classList.add('is-active')
	})
});

