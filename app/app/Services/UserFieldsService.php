<?php

namespace App\Services;


class UserFieldsService
{

    public function getTenantUserFields($userfields){

        $principalList =[
            'firstname',
            'lastname',
            'email',
            'username',
            'creation_date',
            'last_access_date',
            'statut',
            'speex_id',
            'group_id',
            'project_id',
        ];
        return array_merge($principalList, array_keys( $userfields));
    }


    public function getLearnersFilteredItems($items , $userfields){

        $filteredItems = array_map(function ($item) use ($userfields) {
            $dto = [
                'docebo_id' => $item['user_id'],
                'firstname' => $item['first_name'],
                'lastname' => $item['last_name'],
                'email' => $item['email'],
                'username' => $item['username'],
                'creation_date' => $item['creation_date'],
                'last_access_date' => $item['last_access_date'],
                'statut' => $item['last_access_date'] != null ? 'active' : 'inactive',
            ];

            if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
                $dto['matricule'] = $item['field_1'];
            }else{
                $dto['matricule'] = null;
            }

            if (isset($userfields['fonction']) && $userfields['fonction'] === true) {
                $dto['fonction'] = $item['field_32'];
            }else{
                $dto['fonction'] = null;
            }

            if (isset($userfields['direction']) && $userfields['direction'] === true) {
                $dto['direction'] = $item['field_63'];
            }else{
                $dto['direction'] = null;
            }

            if (isset($userfields['categorie']) && $userfields['categorie'] === true) {
                $dto['categorie'] = $item['field_159'];
            }else{
                $dto['categorie'] = null;
            }

            if (isset($userfields['sexe']) && $userfields['sexe'] === true) {
                $dto['sexe'] = $item['field_240'];
            }else{
                $dto['sexe'] = null;
            }

            if (isset($userfields['cin']) && $userfields['cin'] === true) {
                $dto['cin'] = $item['field_271'];
            }else{
                $dto['cin'] = null;
            }

            return $dto;
        }, $items);

        return $filteredItems;

    }

    public function getArchivesFilteredItems($items , $userfields){

        $filteredItems = array_map(function ($item) use ($userfields) {
            $dto = [
                'docebo_id' => $item['user_id'],
                'firstname' => $item['first_name'],
                'lastname' => $item['last_name'],
                'email' => $item['email'],
                'username' => $item['username'],
                'creation_date' => $item['creation_date'],
                'last_access_date' => $item['last_access_date'],
                'statut' => 'archive',
            ];

            if (isset($userfields['matricule']) && $userfields['matricule'] === true) {
                $dto['matricule'] = $item['field_1'];
            }else{
                $dto['matricule'] = null;
            }

            if (isset($userfields['fonction']) && $userfields['fonction'] === true) {
                $dto['fonction'] = $item['field_32'];
            }else{
                $dto['fonction'] = null;
            }

            if (isset($userfields['direction']) && $userfields['direction'] === true) {
                $dto['direction'] = $item['field_63'];
            }else{
                $dto['direction'] = null;
            }

            if (isset($userfields['categorie']) && $userfields['categorie'] === true) {
                $dto['categorie'] = $item['field_159'];
            }else{
                $dto['categorie'] = null;
            }

            if (isset($userfields['sexe']) && $userfields['sexe'] === true) {
                $dto['sexe'] = $item['field_240'];
            }else{
                $dto['sexe'] = null;
            }

            if (isset($userfields['cin']) && $userfields['cin'] === true) {
                $dto['cin'] = $item['field_271'];
            }else{
                $dto['cin'] = null;
            }

            return $dto;
        }, $items);

        return $filteredItems;

    }
}
