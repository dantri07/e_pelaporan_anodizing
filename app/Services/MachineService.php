<?php

namespace App\Services;

use App\Models\Machine;
use Illuminate\Database\Eloquent\Collection;

class MachineService
{
    public function getAllMachines(): Collection
    {
        return Machine::all();
    }

    public function getMachineById(int $id): Machine
    {
        return Machine::findOrFail($id);
    }

    public function createMachine(array $data): Machine
    {
        return Machine::create($data);
    }

    public function updateMachine(int $id, array $data): Machine
    {
        $machine = Machine::findOrFail($id);
        $machine->update($data);
        return $machine;
    }

    public function deleteMachine(int $id): bool
    {
        $machine = Machine::findOrFail($id);
        return $machine->delete();
    }

    public function getMachinesForDataTable()
    {
        return Machine::query();
    }
} 