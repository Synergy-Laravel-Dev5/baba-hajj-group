<?php

namespace App\Http\Controllers;

use App\Models\MealPlan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MealPlanController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('meal_plan_view');
        $mealPlans = MealPlan::latest()->get();
        $trashCount = MealPlan::onlyTrashed()->count();

        return view('meal_plan.index', compact('mealPlans', 'trashCount'));
    }

    public function create()
    {
        $this->authorize('meal_plan_create');
        return view('meal_plan.create');
    }

    public function store(Request $request)
    {
        $this->authorize('meal_plan_create');
        $request->validate([
            'name'        => 'required|string|max:150|unique:meal_plans,name',
            'code'        => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'status'      => 'required|in:active,inactive',
        ]);

        try {
            $data = $request->all();
            if (empty($data['code'])) {
                $data['code'] = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $request->name), 0, 10));
            }

            $mealPlan = MealPlan::create($data);

            if (function_exists('logUserActivity')) {
                logUserActivity('Meal Plan Created', 'Name: ' . $mealPlan->name, $mealPlan->id, 'MealPlan');
            }

            return redirect()->route('meal-plan.index')->with('success', 'Meal Plan created successfully.');
        } catch (Exception $e) {
            Log::error('Meal Plan creation failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error creating meal plan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('meal_plan_edit');
        $mealPlan = MealPlan::findOrFail($id);

        return view('meal_plan.edit', compact('mealPlan'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('meal_plan_edit');
        $mealPlan = MealPlan::findOrFail($id);

        $request->validate([
            'name'        => ['required', 'string', 'max:150', Rule::unique('meal_plans', 'name')->ignore($mealPlan->id)],
            'code'        => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'status'      => 'required|in:active,inactive',
        ]);

        try {
            $data = $request->all();
            if (empty($data['code'])) {
                $data['code'] = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $request->name), 0, 10));
            }

            $mealPlan->update($data);

            if (function_exists('logUserActivity')) {
                logUserActivity('Meal Plan Updated', 'Name: ' . $mealPlan->name, $mealPlan->id, 'MealPlan');
            }

            return redirect()->route('meal-plan.index')->with('success', 'Meal Plan updated successfully.');
        } catch (Exception $e) {
            Log::error('Meal Plan update failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error updating meal plan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('meal_plan_trash');
        try {
            $mealPlan = MealPlan::findOrFail($id);
            $mealPlan->delete();

            if (function_exists('logUserActivity')) {
                logUserActivity('Meal Plan Deleted', 'Name: ' . $mealPlan->name, $mealPlan->id, 'MealPlan');
            }

            return redirect()->route('meal-plan.index')->with('success', 'Meal Plan moved to trash successfully.');
        } catch (Exception $e) {
            Log::error('Meal Plan deletion failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error deleting meal plan.');
        }
    }

    public function trash()
    {
        $this->authorize('meal_plan_trash_view');
        $mealPlans = MealPlan::onlyTrashed()->latest()->get();

        return view('meal_plan.trash', compact('mealPlans'));
    }

    public function restore($id)
    {
        $this->authorize('meal_plan_restore');
        try {
            $mealPlan = MealPlan::onlyTrashed()->findOrFail($id);
            $mealPlan->restore();

            if (function_exists('logUserActivity')) {
                logUserActivity('Meal Plan Restored', 'Name: ' . $mealPlan->name, $mealPlan->id, 'MealPlan');
            }

            return redirect()->route('meal-plan.trash')->with('success', 'Meal Plan restored successfully.');
        } catch (Exception $e) {
            Log::error('Meal Plan restore failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error restoring meal plan.');
        }
    }
}
