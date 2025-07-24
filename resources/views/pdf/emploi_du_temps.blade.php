<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Emploi du temps - Directeur</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 2mm;
        }

        body {
            margin: 0;
            padding: 0;
            font-size: 7px;
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
            font-size: 6px;
        }

        th {
            background-color: #2c3e50;
            color: white;
            border: 0.5px solid #000;
            padding: 1px;
            text-align: center;
            line-height: 1.1;
        }

        td {
            border: 0.5px solid #000;
            padding: 1px;
            text-align: center;
            line-height: 1.1;
        }

        .groupe-cell {
            width: 30px;
            font-weight: bold;
            background-color: #ecf0f1;
        }

        .horaire-cell {
            height: 12px;
            vertical-align: top;
            background-color: #de79e0;
        }

        .total-cell {
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
        }

        .session-details span {
            display: block;
            margin: 0;
            padding: 0;
        }

        @media print {
            body {
                font-size: 6px !important;
            }

            table {
                font-size: 5px !important;
            }
        }
    </style>
</head>

<body>

    <h2>Emploi du temps</h2>
    <p>Établissement: {{ $etablissement->nom }}</p>
    <p>Semaine {{ $semaine->numero_semaine }} du {{ \Carbon\Carbon::parse($semaine->date_debut)->format('d/m/Y') }} au
        {{ \Carbon\Carbon::parse($semaine->date_fin)->format('d/m/Y') }}</p>
    @if ($secteurId)
        <p>Secteur: {{ $secteurNom }}</p>
    @endif

    <div class="emploi-container">
        <table>
            {{-- 1ère ligne : jours + colonnes horaires --}}
            <tr>
                <th rowspan="3">Groupes</th>
                @foreach (['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] as $jour)
                    <th colspan="4">{{ $jour }}</th>
                @endforeach
                <th rowspan="3">total</th>
            </tr>
            <tr class="jour-row">
                @for ($i = 0; $i < 6; $i++)
                    <th colspan="2">AM</th>
                    <th colspan="2">PM</th>
                @endfor
            </tr>
            <tr class="horaire-header">
                @for ($i = 0; $i < 6; $i++)
                    <th>08:30-11:00</th>
                    <th>11:00-13:30</th>
                    <th>13:30-16:00</th>
                    <th>16:00-18:30</th>
                @endfor
            </tr>

            {{-- Données par groupe --}}
            @php
                $timeSlots = [
                    '08:30-11:00' => ['08:30:00', '11:00:00'],
                    '11:00-13:30' => ['11:00:00', '13:30:00'],
                    '13:30-16:00' => ['13:30:00', '16:00:00'],
                    '16:00-18:30' => ['16:00:00', '18:30:00'],
                ];
                $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];

                // Prepare sessions for easy lookup
                $groupedSessions = [];
                foreach ($seances as $seance) {
                    $dayOfWeek = \Carbon\Carbon::parse($seance->date_seance)->dayName;
                    $dayOfWeekFr = str_replace(
                        ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                        ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'],
                        $dayOfWeek,
                    );
                    $groupeNom = $seance->groupe->nom;
                    $heureDebut = $seance->heure_debut;
                    $heureFin = $seance->heure_fin;

                    foreach ($timeSlots as $slotName => $slotTimes) {
                        $slotStart = \Carbon\Carbon::parse($slotTimes[0]);
                        $slotEnd = \Carbon\Carbon::parse($slotTimes[1]);
                        $seanceStart = \Carbon\Carbon::parse($heureDebut);
                        $seanceEnd = \Carbon\Carbon::parse($heureFin);

                        if ($seanceStart->lessThan($slotEnd) && $seanceEnd->greaterThan($slotStart)) {
                            $groupedSessions[$groupeNom][$dayOfWeekFr][$slotName][] = $seance;
                        }
                    }
                }

                $allGroupNames = $groupes->pluck('nom')->unique()->sort()->toArray();
            @endphp

            @foreach ($allGroupNames as $groupe)
                @php
                    $totalHeures = 0;
                @endphp
                <tr>
                    <td class="groupe-cell">{{ $groupe }}</td>
                    @foreach ($days as $jour)
                        @foreach ($timeSlots as $slotName => $slotTimes)
                            <td class="horaire-cell">
                                @if (isset($groupedSessions[$groupe][$jour][$slotName]))
                                    @foreach ($groupedSessions[$groupe][$jour][$slotName] as $seance)
                                        @php
                                            $totalHeures += 2.5;
                                        @endphp
                                        <div class="session-details">
                                            <span>
                                                <strong>
                                                    @if ($seance->formateur && $seance->formateur->utilisateur)
                                                        {{ substr($seance->formateur->utilisateur->nom, 0, 15) }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </strong>
                                            </span>
                                            <hr />
                                            <span><strong>{{ $seance->module->nom }}</strong></span>
                                            <hr />
                                            <span>
                                                <strong>
                                                    @if ($seance->type == 'presentiel')
                                                        {{ substr($seance->salle->nom ?? 'N/A', 0, 10) }}
                                                    @else
                                                        Teams
                                                    @endif
                                                </strong>
                                            </span>
                                        </div>
                                    @endforeach
                                @endif
                            </td>
                        @endforeach
                    @endforeach
                    <td class="horaire-cell total-cell"><strong>{{ intval($totalHeures) }} heure</strong></td>
                </tr>
            @endforeach

        </table>
    </div>

</body>

</html>
