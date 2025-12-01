document.addEventListener("DOMContentLoaded", () => {
    // Sélectionnez le formulaire de création de cours
    // Assurez-vous que l'ID 'createCourseForm' existe dans votre HTML de tableau de bord
    const createCourseForm = document.getElementById("createCourseForm"); 

    if (createCourseForm) {
        createCourseForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            // 1. Récupérer les données du formulaire
            const courseCode = document.getElementById("courseCode").value.trim();
            const courseName = document.getElementById("courseName").value.trim();
            const description = document.getElementById("courseDescription").value.trim(); 

            // 2. Validation côté client (minimaliste)
            if (!courseCode || !courseName || !description) {
                Swal.fire('Erreur', 'Veuillez remplir tous les champs du cours.', 'error');
                return;
            }

            // 3. Préparer les données pour l'envoi
            const courseData = {
                code: courseCode,
                name: courseName,
                description: description
            };

            try {
                // 4. Envoi des données au script PHP via Fetch API
                const response = await fetch('../php/create_course.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(courseData)
                });

                // 5. Gérer la réponse JSON
                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        title: 'Succès!',
                        text: result.message,
                        icon: 'success'
                    }).then(() => {
                        // Optionnel : Réinitialiser le formulaire ou recharger la liste des cours
                        createCourseForm.reset();
                        // rechargerLaListeDesCours(); 
                    });
                } else {
                    Swal.fire('Échec', result.message, 'error');
                }
            } catch (error) {
                console.error('Erreur lors de la requête de création de cours:', error);
                Swal.fire('Erreur Système', 'Impossible de contacter le serveur.', 'error');
            }
        });
    } else {
        console.warn("Formulaire 'createCourseForm' non trouvé sur cette page.");
    }
});