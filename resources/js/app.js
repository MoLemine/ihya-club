import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const hospitalSelect = document.getElementById('hospital-select');
    const hospitalOtherWrapper = document.getElementById('hospital-other-wrapper');

    if (hospitalSelect && hospitalOtherWrapper) {
        const syncHospitalField = () => {
            hospitalOtherWrapper.classList.toggle('hidden', hospitalSelect.value !== 'Autre');
        };

        hospitalSelect.addEventListener('change', syncHospitalField);
        syncHospitalField();
    }

    const imageModal = document.querySelector('[data-image-modal]');
    const imageModalImage = document.querySelector('[data-image-modal-image]');

    if (imageModal && imageModalImage) {
        document.querySelectorAll('[data-image-modal-open]').forEach((button) => {
            button.addEventListener('click', () => {
                imageModalImage.src = button.dataset.imageSrc || '';
                imageModalImage.alt = button.dataset.imageAlt || '';
                imageModal.classList.remove('hidden');
            });
        });

        const closeModal = () => {
            imageModal.classList.add('hidden');
            imageModalImage.src = '';
            imageModalImage.alt = '';
        };

        document.querySelectorAll('[data-image-modal-close]').forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        imageModal.addEventListener('dblclick', closeModal);
        imageModal.addEventListener('click', (event) => {
            if (event.target === imageModal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    }
});
