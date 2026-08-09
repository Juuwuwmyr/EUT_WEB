<?php

/**
 * Naujan, Oriental Mindoro — Barangay Delivery Fee Table
 *
 * Formula:
 * - ₱30 base fee for 0–2 km
 * - +₱10 for every additional kilometer after 2 km
 *
 * Distances are approximate straight-line distances from:
 * EUT Snack House
 * Coordinates: 13.321512, 121.302098
 *
 * NOTE:
 * These are FALLBACK fees when the customer has no GPS coordinates.
 * Actual GPS-based delivery fee should be calculated in the
 * OrderController and checkout JavaScript.
 */

return [

    // ── Poblacion / Town Center ────────────────────────────────
    'Poblacion I (Barangay I)'          => 30,  // 0.0 km
    'Poblacion II (Barangay II)'        => 30,  // 0.1 km
    'Poblacion III (Barangay III)'      => 30,  // 0.1 km

    // ── ₱40 ────────────────────────────────────────────────────
    'Andres Ylagan (Mag-asawang Tubig)'  => 30,  // 1.1 km
    'San Antonio'                        => 50,  // 4 km

    // ── ₱50 ────────────────────────────────────────────────────
    'Estrella'                           => 50,  // 4 km
    'Kalinisan'                          => 60,  // 5.0 km
    'Santa Cruz'                         => 60,  // 5 km
    'Santa Maria'                        => 40,  // 3 km

    // ── ₱70 ────────────────────────────────────────────────────
    'Nag-Iba I'                          => 70,  // 6 km
    'Mabini'                             => 80,  // 7 km
    'Motoderazo'                         => 80,  // 7 km
    'San Carlos'                         => 80,  // 7 km

    // ── ₱90 ────────────────────────────────────────────────────
    'San Jose (San Jose Uno)'            => 90,  // 8 km
    'Bacungan'                           => 70,  // 6 km
    'Buhangin'                           => 90,  // 8 km
    'Bancuro'                            => 100, // 9 km
    'Antipolo (Parusan)'                 => 100, // 9 km
    'San Agustin I'                      => 100, // 9 km

    // ── ₱110 ───────────────────────────────────────────────────
    'Gamao'                              => 110, // 10 km
    'San Agustin II (Ilaya)'             => 110, // 10 km
    'Pinagsabangan II'                   => 110, // 10 km
    'San Isidro (Calaguimay)'            => 110, // 10 km
    'Melgar A (San Jose Dos)'            => 110, // 10 km

    // ── ₱120 ───────────────────────────────────────────────────
    'Sampaguita'                         => 120, // 11 km
    'Concepcion'                         => 120, // 11 km
    'Pinahan'                            => 120, // 11 km
    'Pinagsabangan I'                    => 120, // 11 km

    // ── ₱140 ───────────────────────────────────────────────────
    'Dao'                                => 140, // 11.4 km → rounded up to 12 km

    // ── ₱150 ───────────────────────────────────────────────────
    'Barcenaga'                          => 130, // 12 km

];
