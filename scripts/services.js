document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('confirmationModal');
    const confirmYes = document.getElementById('confirmYes');
    const confirmNo = document.getElementById('confirmNo');
    let activeForm = null;

    document.querySelectorAll('.package-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            activeForm = this;
            modal.style.display = 'flex';
        });
    });

    confirmYes.addEventListener('click', function() {
        if (activeForm) {
            activeForm.submit();
        }
        modal.style.display = 'none';
    });

    confirmNo.addEventListener('click', function() {
        modal.style.display = 'none';
        activeForm = null;
    });

    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            activeForm = null;
        }
    });
});