<?php

function enregistrerLogMongoDB(string $action, string $details = '', ?int $utilisateurId = null): bool
{
    try {
        $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');

        $bulk = new MongoDB\Driver\BulkWrite();

        $bulk->insert([
            'action' => $action,
            'details' => $details,
            'utilisateur_id' => $utilisateurId,
            'date' => new MongoDB\BSON\UTCDateTime()
        ]);

        $manager->executeBulkWrite('fantasyrealm_logs.logs', $bulk);

        return true;
    } catch (Throwable $e) {
        error_log('Erreur MongoDB : ' . $e->getMessage());
        return false;
    }
}