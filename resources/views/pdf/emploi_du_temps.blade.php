<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            inline-size: 100%;
            border-collapse: collapse;
            margin-block-start: 10px;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 6px;
            text-align: center;
        }
    </style>
</head>

<body>
    <h2>Emploi du Temps - {{ $etablissement->nom }}</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Semaine</th>
                <th>Heure</th>
                <th>Module</th>
                <th>Formateur</th>
                <th>Groupe</th>
                <th>Salle</th>
                <th>Type</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($seances as $seance)
                <tr>
                    <td>{{ $seance->date_seance }}</td>
                    <td>{{ $seance->semaine->numero_semaine }}</td>
                    <td>{{ $seance->heure_debut }} - {{ $seance->heure_fin }}</td>
                    <td>{{ $seance->module->nom }}</td>
                    <td>{{ $seance->formateur->utilisateur->nom }}</td>
                    <td>{{ $seance->groupe->nom }}</td>
                    <td>{{ $seance->salle->nom ?? '—' }}</td>
                    <td>{{ ucfirst($seance->type) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
