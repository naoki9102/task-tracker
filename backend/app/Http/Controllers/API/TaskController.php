<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Task::all();
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_description' => 'required|string',
            'date' => 'required|date',
            'hourly_rate' => 'required|numeric',
            'additional_charges' => 'nullable|numeric',
            'is_multiple_employees' => 'boolean',
            'employee_contributions' => 'nullable|array'
        ]);

        // Validasi tambahan berdasarkan is_multiple_employees
        if ($request->is_multiple_employees) {
            $request->validate([
                'employee_contributions' => 'required|array|min:1',
                'employee_contributions.*.name' => 'required|string',
                'employee_contributions.*.hours' => 'required|numeric|min:0'
            ]);
        } else {
            $request->validate([
                'employee_name' => 'required|string',
                'hours_spent' => 'required|numeric|min:0'
            ]);
        }

        // Hitung total jam kerja dan biaya dasar
        $totalHours = $request->is_multiple_employees ? 
            array_sum(array_column($request->employee_contributions, 'hours')) : 
            $request->hours_spent;
            
        $baseCost = $totalHours * $request->hourly_rate;
        $additionalCharges = $request->additional_charges ?? 0;
        $totalCost = $baseCost + $additionalCharges;

        if ($request->is_multiple_employees && !empty($request->employee_contributions)) {
            $tasks = [];
            
            foreach ($request->employee_contributions as $contribution) {
                // Hitung persentase kontribusi
                $percentage = $contribution['hours'] / $totalHours;
                
                // Hitung remunerasi berdasarkan persentase
                $baseRemuneration = round($baseCost * $percentage, 2);
                $additionalRemuneration = round($additionalCharges * $percentage, 2);
                $totalRemuneration = $baseRemuneration + $additionalRemuneration;
                
                // Simpan detail perhitungan
                $calculationDetails = [
                    'base_cost' => $baseCost,
                    'additional_charges' => $additionalCharges,
                    'total_cost' => $totalCost,
                    'employee_percentage' => $percentage * 100,
                    'base_remuneration' => $baseRemuneration,
                    'additional_remuneration' => $additionalRemuneration,
                    'total_remuneration' => $totalRemuneration
                ];
                
                $taskData = [
                    'employee_name' => $contribution['name'],
                    'task_description' => $request->task_description,
                    'date' => $request->date,
                    'hours_spent' => $contribution['hours'],
                    'hourly_rate' => $request->hourly_rate,
                    'additional_charges' => $additionalRemuneration,
                    'total_remuneration' => $totalRemuneration,
                    'is_multiple_employees' => true,
                    'calculation_details' => json_encode($calculationDetails)
                ];
                
                $tasks[] = Task::create($taskData);
            }
            
            return $tasks;
        } else {
            $validated['employee_name'] = $request->employee_name;
            $validated['hours_spent'] = $request->hours_spent;
            $validated['additional_charges'] = $additionalCharges;
            $validated['total_remuneration'] = $totalCost;
            $validated['calculation_details'] = json_encode([
                'base_cost' => $baseCost,
                'additional_charges' => $additionalCharges,
                'total_cost' => $totalCost
            ]);
            
            return Task::create($validated);
        }
    }
    
    public function show(Task $task)
    {
        return $task;
    }
    
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'task_description' => 'required|string',
            'date' => 'required|date',
            'hourly_rate' => 'required|numeric',
            'additional_charges' => 'nullable|numeric',
            'is_multiple_employees' => 'boolean',
            'employee_contributions' => 'nullable|array'
        ]);

        // Validasi tambahan berdasarkan is_multiple_employees
        if ($request->is_multiple_employees) {
            $request->validate([
                'employee_contributions' => 'required|array|min:1',
                'employee_contributions.*.name' => 'required|string',
                'employee_contributions.*.hours' => 'required|numeric|min:0'
            ]);
        } else {
            $request->validate([
                'employee_name' => 'required|string',
                'hours_spent' => 'required|numeric|min:0'
            ]);
        }

        // Hitung total jam kerja dan biaya dasar
        $totalHours = $request->is_multiple_employees ? 
            array_sum(array_column($request->employee_contributions, 'hours')) : 
            $request->hours_spent;
            
        $baseCost = $totalHours * $request->hourly_rate;
        $additionalCharges = $request->additional_charges ?? 0;
        $totalCost = $baseCost + $additionalCharges;

        if ($request->is_multiple_employees && !empty($request->employee_contributions)) {
            // Hapus task lama
            $task->delete();
            
            $tasks = [];
            
            foreach ($request->employee_contributions as $contribution) {
                // Hitung persentase kontribusi
                $percentage = $contribution['hours'] / $totalHours;
                
                // Hitung remunerasi berdasarkan persentase
                $baseRemuneration = round($baseCost * $percentage, 2);
                $additionalRemuneration = round($additionalCharges * $percentage, 2);
                $totalRemuneration = $baseRemuneration + $additionalRemuneration;
                
                // Simpan detail perhitungan
                $calculationDetails = [
                    'base_cost' => $baseCost,
                    'additional_charges' => $additionalCharges,
                    'total_cost' => $totalCost,
                    'employee_percentage' => $percentage * 100,
                    'base_remuneration' => $baseRemuneration,
                    'additional_remuneration' => $additionalRemuneration,
                    'total_remuneration' => $totalRemuneration
                ];
                
                $taskData = [
                    'employee_name' => $contribution['name'],
                    'task_description' => $request->task_description,
                    'date' => $request->date,
                    'hours_spent' => $contribution['hours'],
                    'hourly_rate' => $request->hourly_rate,
                    'additional_charges' => $additionalRemuneration,
                    'total_remuneration' => $totalRemuneration,
                    'is_multiple_employees' => true,
                    'calculation_details' => json_encode($calculationDetails)
                ];
                
                $tasks[] = Task::create($taskData);
            }
            
            return $tasks;
        } else {
            $validated['employee_name'] = $request->employee_name;
            $validated['hours_spent'] = $request->hours_spent;
            $validated['additional_charges'] = $additionalCharges;
            $validated['total_remuneration'] = $totalCost;
            $validated['calculation_details'] = json_encode([
                'base_cost' => $baseCost,
                'additional_charges' => $additionalCharges,
                'total_cost' => $totalCost
            ]);
            
            $task->update($validated);
            return $task;
        }
    }
    
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['message' => 'Deleted']);
    }    
}
