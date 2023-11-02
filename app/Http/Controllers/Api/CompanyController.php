<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Service\Media;
use App\Models\Company;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Http\Resources\CompanyResource;

class CompanyController extends Controller
{


    public function __construct(
        private Media $mediService
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $company = Company::latest()->paginate(10);
        return CompanyResource::collection($company);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyRequest $request)
    {
        $validatedRequest = $request->validated();
        if ($request->hasFile('logo')) {
            $validatedRequest['logo'] = $this->mediService->upload($request->file('logo'));
        }

        $company = Company::create($validatedRequest);
        return new CompanyResource($company);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $company = Company::find($id);
        return new CompanyResource($company);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyRequest $request, string $id)
    {
        try {
            if ($company = Company::find($id)) {

                $validatedRequest = $request->validated();
                if ($request->hasFile('logo')) {
                    $this->mediService->delete($company->logo);
                    $validatedRequest['logo'] = $this->mediService->upload($request->file('logo'));
                }

                $companyUpdated = $company->update($validatedRequest);

                if($companyUpdated) {
                    return response()->json(['suceess'=> true, 'message' => 'updated successfully,']);
                }

                throw new Exception("company {$company->name} update failed!");
            } else {
                throw new Exception("company record not found!");
            }
        } catch (\Exception $exception) {
            return response()->json(["success" => false, "message" => $exception->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {

            if ($company = Company::find($id)) {
                if ($company->logo) {
                    $this->mediService->delete($company->logo);
                }

                $companyDeleted = $company->delete();

                if($companyDeleted) {
                    return response()->json(['suceess'=> true, 'message' => "{$company->name} deleted successfully."]);
                }

                throw new Exception("company {$company->name} delete failed!");
            } else {
                throw new Exception("company record not found!");
            }
        } catch (\Exception $exception) {
            return response()->json(["success" => false, "message" => $exception->getMessage()]);
        }

    }
}
