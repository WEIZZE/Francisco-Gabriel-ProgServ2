<?php
echo "8d27fc6d88db4c7a99e31e455461ec6e";
echo "<br> <br>";


$curl = curl_init(
    'https://api.football-data.org/v4/competitions/PL/matches?status=FINISHED'
);

curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['X-Auth-Token: 8d27fc6d88db4c7a99e31e455461ec6e'],
    CURLOPT_TIMEOUT => 15,
]);

$response = curl_exec($curl);

if ($response === false) {
    exit('Erreur cURL : ' . curl_error($curl));
}

$data = json_decode($response, true);

if (!isset($data['matches'])) {
    exit('Réponse inattendue : ' . htmlspecialchars($response));
}

echo "<h2>Matchs de Premier League</h2>";

foreach ($data['matches'] as $match) {
    $domicile = $match['homeTeam']['name'];
    $exterieur = $match['awayTeam']['name'];

    $scoreDomicile = $match['score']['fullTime']['home'];
    $scoreExterieur = $match['score']['fullTime']['away'];

    $ligne = $domicile . ' '
        . $scoreDomicile . ' - ' . $scoreExterieur
        . ' ' . $exterieur;

    echo htmlspecialchars($ligne) . "<br>\n";
}
