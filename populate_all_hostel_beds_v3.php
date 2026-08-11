<?php
include 'connection/connect.php';

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// Clear existing beds for target hostels
$hostels_to_clear = ['C1', 'C4', 'C7', 'C8', 'B3', 'B4', 'B5'];
foreach ($hostels_to_clear as $h) {
    $conn->query("DELETE FROM hostel_bed WHERE hos_id = '$h'");
    echo "Cleared existing beds for $h.<br>";
}

// Update hostel records
$hostels_to_add = [
    ['C1', 1, 'f'],
    ['C4', 1, 'f'],
    ['C7', 3, 'f'],
    ['C8', 4, 'f'],
    ['B3', 2, 'f'],
    ['B4', 3, 'f'],
    ['B5', 3, 'f']
];
foreach ($hostels_to_add as $h) {
    $check = $conn->query("SELECT * FROM hostel WHERE hos_id = '{$h[0]}'");
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO hostel (hos_id, hos_floors, gender) VALUES ('{$h[0]}', {$h[1]}, '{$h[2]}')");
        echo "Added hostel {$h[0]}<br>";
    } else {
        $conn->query("UPDATE hostel SET hos_floors = {$h[1]}, gender = '{$h[2]}' WHERE hos_id = '{$h[0]}'");
        echo "Updated hostel {$h[0]}<br>";
    }
}

// Update D5 gender
$conn->query("UPDATE hostel SET gender = 'f' WHERE hos_id = 'D5'");
echo "Updated D5 gender to female.<br>";

// ========== ROOM CONFIGURATIONS ==========
$configs = [];

// C1
$c1_rooms = array_merge(range(1,9), range(11,13));
$configs[] = ['C1', 0, $c1_rooms, 2, []];

// C4
$c4_rooms = [];
foreach ([[2,7],[10,12],[14,16],[20,24],[27,32]] as $r) {
    $c4_rooms = array_merge($c4_rooms, range($r[0], $r[1]));
}
$configs[] = ['C4', 0, $c4_rooms, 2, []];

// C7
$configs[] = ['C7', 0, range(1,4), 4, []];
$configs[] = ['C7', 0, [7,8,9,10], 4, [7 => 1]];
$configs[] = ['C7', 1, array_merge(range(1,5), range(7,11)), 4, []];
$configs[] = ['C7', 2, array_merge(range(1,5), range(6,10)), 4, []];

// C8
$configs[] = ['C8', 0, range(1,16), 4, []];
$configs[] = ['C8', 1, range(17,44), 4, []];
$configs[] = ['C8', 2, range(45,72), 4, []];
$configs[] = ['C8', 3, array_merge(range(73,99), [100]), 4, []];

// B3
$b3_floor1 = [202,203,204,205,206,207,211,212,213,214,215,216,218,219,220,221,222,223,225,226,227,228,229,230];
$b3_floor2 = [302,303,304,305,306,307,308,309,313,314,315,316,317,318,319,320,321,322,323,324,326,327,328,329,330,331];
$configs[] = ['B3', 1, $b3_floor1, 2, []];
$configs[] = ['B3', 2, $b3_floor2, 2, []];

// B4 – corrected (excluded sick/warden rooms)
$b4_floor0 = [1,2,3,4,5,8,9,10,12,13,14,18,19,20,21,22];
$b4_floor1 = [101,102,103,107,108,109,110,111,112,113,114,115,116,117,120,121,122];
$b4_floor2 = [201,202,203,207,208,209,210,211,212,213,214,215,216,217,220,221,222];
$configs[] = ['B4', 0, $b4_floor0, 2, []];
$configs[] = ['B4', 1, $b4_floor1, 2, [112 => 4]];
$configs[] = ['B4', 2, $b4_floor2, 2, [212 => 4]];

// B5 – corrected (excluded warden rooms)
$b5_floor0 = [1,2,3,4,6,7,8,10,11,12,15,16,17,18];
$b5_floor1 = [101,102,103,106,107,108,109,110,111,112,113,114,116,117,118];
$b5_floor2 = [201,202,203,206,207,208,209,210,211,212,213,214,216,217,218];
$configs[] = ['B5', 0, $b5_floor0, 2, []];
$configs[] = ['B5', 1, $b5_floor1, 2, [110 => 4]];
$configs[] = ['B5', 2, $b5_floor2, 2, [210 => 4]];

// Insert beds
$inserted = 0;
$duplicates = 0;
foreach ($configs as $cfg) {
    list($hos_id, $floor, $rooms, $default_beds, $special_beds) = $cfg;
    foreach ($rooms as $room) {
        $beds_for_this_room = isset($special_beds[$room]) ? $special_beds[$room] : $default_beds;
        for ($bed = 1; $bed <= $beds_for_this_room; $bed++) {
            $sql = "INSERT IGNORE INTO hostel_bed (hos_id, floor_no, room_no, bed_no, availability)
                    VALUES ('$hos_id', $floor, $room, $bed, 1)";
            if ($conn->query($sql)) {
                if ($conn->affected_rows > 0) {
                    $inserted++;
                } else {
                    $duplicates++;
                }
            } else {
                echo "Error: " . $conn->error . " for hos_id $hos_id, room $room, bed $bed<br>";
            }
        }
    }
}

echo "<br>✅ Inserted $inserted new beds (skipped $duplicates duplicates).";

$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// Summary
echo "<hr><h3>Summary per Hostel:</h3>";
$summary = $conn->query("SELECT hos_id, COUNT(*) AS total FROM hostel_bed GROUP BY hos_id ORDER BY hos_id");
while ($row = $summary->fetch_assoc()) {
    echo "{$row['hos_id']}: {$row['total']} beds<br>";
}
?>