<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>Emploi du temps - Stagiaire</title>
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 10px;
    }

    h2 {
      margin-bottom: 5px;
    }

    p {
      margin: 0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    th,
    td {
      border: 1px solid #999;
      padding: 5px;
      vertical-align: top;
      text-align: center;
    }

    th {
      background-color: #f2f2f2;
    }

    .seance-card {
      padding: 3px;
      margin-bottom: 3px;
    }

    .module {
      color: #6b46c1;
      font-weight: bold;
    }

    .formateur {
      font-style: italic;
      font-size: 9px;
      color: black;
    }

    .distanciel {
      color: #c53030;
      background-color: #fed7d7;
      border-radius: 3px;
      padding: 1px 3px;
      font-size: 10px;
      display: inline-block;
      margin-top: 2px;
    }

    .presentiel {
      color: #2f855a;
      background-color: #c6f6d5;
      border-radius: 3px;
      padding: 1px 3px;
      font-size: 10px;
      display: inline-block;
      margin-top: 2px;
    }
  </style>
</head>

<body>
  <h2>Emploi du temps du groupe : {{ $groupe->nom }}</h2>
  <p>Semaine {{ $semaine->numero_semaine }} :
    du {{ \Carbon\Carbon::parse($semaine->date_debut)->format('d/m/Y') }}
    au {{ \Carbon\Carbon::parse($semaine->date_fin)->format('d/m/Y') }}</p>

  @php
    $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
    $creneaux = ['08:30 - 10:50', '11:10 - 13:30', '13:30 - 15:50', '16:10 - 18:30'];
    $organized = [];

    foreach ($jours as $j => $jour) {
        foreach ($creneaux as $c => $creneau) {
            $organized[$j][$c] = [];
        }
    }

    foreach ($seances as $seance) {
        $day = \Carbon\Carbon::parse($seance->date_seance)->dayOfWeekIso - 1;
        $start = substr($seance->heure_debut, 0, 5);
        $end = substr($seance->heure_fin, 0, 5);
        $index = array_search("$start - $end", $creneaux);

        if ($day >= 0 && $day < 6 && $index !== false) {
            $organized[$day][$index][] = $seance;
        }
    }
  @endphp

  <table>
    <thead>
      <tr>
        <th>Jour / Créneau</th>
        @foreach ($creneaux as $creneau)
          <th>{{ $creneau }}</th>
        @endforeach
      </tr>
    </thead>
    <tbody>
      @foreach ($jours as $jIndex => $jour)
        <tr>
          <td><strong>{{ $jour }}</strong></td>
          @foreach ($creneaux as $cIndex => $creneau)
            <td>
              @forelse ($organized[$jIndex][$cIndex] as $seance)
                <div class="seance-card">
                  <div class="formateur">{{ $seance->formateur->utilisateur->nom ?? 'Formateur inconnu' }}</div>
                  <div class="module">{{ $seance->module->nom ?? 'Module inconnu' }}</div>
                  @if (strtolower($seance->type) === 'distanciel')
                    <div class="distanciel">Distanciel</div>
                  @else
                    <div class="presentiel">{{ $seance->salle->nom ?? 'Salle inconnue' }}</div>
                  @endif
                </div>
              @empty
                -
              @endforelse
            </td>
          @endforeach
        </tr>
      @endforeach
    </tbody>
  </table>
</body>

</html>
