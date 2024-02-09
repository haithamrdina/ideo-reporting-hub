<div class="page-header d-print-none text-white">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    IDEO Reporting
                </div>
                <h2 class="page-title">
                    Vos données de progression sur la e-académie.
                </h2>
            </div>
            @if (isset($projects) || isset($groupes))
            @endif
            @isset($projects)
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <div class="mb-3 text-black">
                            <div class="input-group mb-2">
                                <span class="input-group-text">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-binary-tree"
                                        width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="#C2181A"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M6 20a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z" />
                                        <path d="M16 4a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z" />
                                        <path d="M16 20a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z" />
                                        <path d="M11 12a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z" />
                                        <path d="M21 12a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z" />
                                        <path d="M5.058 18.306l2.88 -4.606" />
                                        <path d="M10.061 10.303l2.877 -4.604" />
                                        <path d="M10.065 13.705l2.876 4.6" />
                                        <path d="M15.063 5.7l2.881 4.61" />
                                    </svg>
                                </span>
                                <select type="text" class="form-select" id="select-branches" wire:model.live="selectedProject">
                                    @foreach ($projects as $projectData)
                                        <option value="{{ $projectData->id }}"> {{ $projectData->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            @endisset
            @isset($groupes)
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <div class="mb-3 text-black">
                            <div class="input-group mb-2">
                                <span class="input-group-text">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-affiliate"
                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="#C2181A"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M5.931 6.936l1.275 4.249m5.607 5.609l4.251 1.275"></path>
                                        <path d="M11.683 12.317l5.759 -5.759"></path>
                                        <path d="M5.5 5.5m-1.5 0a1.5 1.5 0 1 0 3 0a1.5 1.5 0 1 0 -3 0"></path>
                                        <path d="M18.5 5.5m-1.5 0a1.5 1.5 0 1 0 3 0a1.5 1.5 0 1 0 -3 0"></path>
                                        <path d="M18.5 18.5m-1.5 0a1.5 1.5 0 1 0 3 0a1.5 1.5 0 1 0 -3 0"></path>
                                        <path d="M8.5 15.5m-4.5 0a4.5 4.5 0 1 0 9 0a4.5 4.5 0 1 0 -9 0"></path>
                                    </svg>
                                </span>
                                <select type="text" class="form-select" id="select-groupes">
                                    @foreach ($groupes as $groupeData)
                                        <option value="{{ $groupeData->id }}"> {{ $groupeData->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            @endisset
        </div>
    </div>
</div>
