function togglePassword(id) {
      const field = document.getElementById(id);
      field.type = field.type === "password" ? "text" : "password";
    }

    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirmPassword');
    const matchMessage = document.getElementById('passwordMatchMessage');

    function checkPasswordsMatch() {
      if (confirmPassword.value === '') {
        matchMessage.textContent = '';
        return;
      }
      if (password.value === confirmPassword.value) {
        matchMessage.textContent = '✔ Passwords match';
        matchMessage.className = 'match-message match';
      } else {
        matchMessage.textContent = '✖ Passwords do not match';
        matchMessage.className = 'match-message no-match';
      }
    }

    password.addEventListener('input', checkPasswordsMatch);
    confirmPassword.addEventListener('input', checkPasswordsMatch);

    // Prevent form submission if passwords don't match
    document.getElementById('signupForm').addEventListener('submit', function (e) {
      if (password.value !== confirmPassword.value) {
        e.preventDefault();
        matchMessage.textContent = '✖ Passwords do not match';
        matchMessage.className = 'match-message no-match';
      }
    });

    const passwordInput = document.getElementById('password');
    const lengthEl = document.getElementById('length');
    const uppercaseEl = document.getElementById('uppercase');
    const numberEl = document.getElementById('number');
    const specialEl = document.getElementById('special');
    const submitBtn = document.getElementById('submitBtn');

    passwordInput.addEventListener('input', () => {
      const value = passwordInput.value;

      const lengthValid = value.length >= 8;
      const uppercaseValid = /[A-Z]/.test(value);
      const numberValid = /\d/.test(value);
      const specialValid = /[!@#$%^&*]/.test(value);

      lengthEl.className = lengthValid ? 'valid' : 'invalid';
      uppercaseEl.className = uppercaseValid ? 'valid' : 'invalid';
      numberEl.className = numberValid ? 'valid' : 'invalid';
      specialEl.className = specialValid ? 'valid' : 'invalid';

      // Enable the submit button only if all requirements are met
      submitBtn.disabled = !(lengthValid && uppercaseValid && numberValid && specialValid);
    });