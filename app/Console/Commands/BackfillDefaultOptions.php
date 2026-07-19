<?php

namespace App\Console\Commands;

use App\Models\MenuItem;
use App\Models\ModifierGroup;
use App\Models\ModifierOption;
use Illuminate\Console\Command;

class BackfillDefaultOptions extends Command
{
    protected $signature   = 'menu:backfill-defaults';
    protected $description = 'Ensure every modifier group has a "No X" default option as the first choice';

    public function handle(): void
    {
        $groups = ModifierGroup::with('options')->get();
        $added  = 0;

        foreach ($groups as $group) {
            // Label based on type
            $defaultLabel = match($group->type) {
                'flavor'   => 'No Flavor',
                'modifier' => 'No ' . $group->name,   // e.g. "No Size", "No Add-on"
                'addon'    => 'No Add-on',
                default    => 'None',
            };

            // Check if a "No X" / none-priced default already exists
            $hasDefault = $group->options->contains(fn($o) => $o->is_default && $o->price_type === 'none');

            if (! $hasDefault) {
                // Push all existing options' sort_order down by 1
                ModifierOption::where('modifier_group_id', $group->id)
                    ->increment('sort_order');

                // Unset any previous is_default
                ModifierOption::where('modifier_group_id', $group->id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);

                ModifierOption::create([
                    'modifier_group_id' => $group->id,
                    'name'              => $defaultLabel,
                    'price_type'        => 'none',
                    'price_adjustment'  => 0,
                    'is_default'        => true,
                    'is_active'         => true,
                    'sort_order'        => 0,
                ]);

                $added++;
                $this->line("  ✓ Added \"{$defaultLabel}\" to group \"{$group->name}\" (item #{$group->menu_item_id})");
            }
        }

        $this->info("Done. Added default options to {$added} group(s).");
    }
}
