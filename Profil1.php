<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Profil</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden flex max-w-4xl w-full">
        <!-- Section Image -->
        <div class="w-1/2 bg-gray-100 flex items-center justify-center p-6">
            <img src="uploads/image.png" alt="Modification de Profil" class="max-w-xs">
        </div>
        
        <!-- Formulaire -->
        <div class="w-1/2 p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Modifier votre Profil</h2>
            <form action="Profil.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
                    <input type="text" id="nom" name="nom" class="mt-1 p-2 w-full border rounded-md" required>
                </div>
                <div>
                    <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom</label>
                    <input type="text" id="prenom" name="prenom" class="mt-1 p-2 w-full border rounded-md" required>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" class="mt-1 p-2 w-full border rounded-md" required>
                </div>
                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700">Photo de profil</label>
                    <input type="file" id="photo" name="photo" class="mt-1 p-2 w-full border rounded-md">
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">Mettre à jour</button>
            </form>
        </div>
    </div>
</body>
</html>
