<?php

namespace App\Http\Integrations\IdeoDash\Requests;

use App\Models\Learner;
use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class IdeoDashCallsList extends Request
{
    /**
     * The HTTP method of the request
     */
    protected $docebo_org_id;
    protected Method $method = Method::GET;

    public function __construct(string $docebo_org_id)
    {
        $this->docebo_org_id = $docebo_org_id;
    }

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/client-calls/'.$this->docebo_org_id;
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        $items = $response->json('Data');
        $filteredItems = array_map(function ($item) {
            $learner = Learner::where('docebo_id' , $item['idDocebo'])->first();
            if($learner){
                return [
                    'learner_docebo_id' => $item['idDocebo'],
                    'group_id' => $learner->group_id,
                    'project_id' =>$learner->project_id,
                    'date_call' => (new DateTime($item['date']))->format("Y-m-d H:i:s"),
                    'type' => $item['type'] == 1 ? 'sortante' : 'entrante',
                    'status' => $this->getstatut($item['statut']),
                    'subject' => $item['sujet']
                ];
            }
        }, $items);

        return $filteredItems;
    }

    protected function getstatut($statut){
        if(in_array($statut, ['Réalisé'])){
            $statutLabel = 'Réalisé';
        }elseif(in_array($statut, ['Occupé', 'Busy Line'])){
            $statutLabel = 'Occupé';
        }elseif(in_array($statut, ['A Rappeler'])){
            $statutLabel = 'Demande de rappel';
        }elseif(in_array($statut, ['Zendesk'])){
            $statutLabel = 'Ticket';
        }elseif(in_array($statut, ['SSR', 'No answer'])){
            $statutLabel = 'Sonnerie sans réponse';
        }elseif(in_array($statut, ['BV', 'Straight to Voicemail'])){
            $statutLabel = 'Boîte vocale';
        }elseif(in_array($statut, ['NE'])){
            $statutLabel = 'Numéro erroné';
        }elseif(in_array($statut, ['NNA'])){
            $statutLabel = 'Numéro non attribué';
        }elseif(in_array($statut, ['Coaching session'])){
            $statutLabel = 'Session de coaching';
        }elseif(in_array($statut, ['Oral Test'])){
            $statutLabel = 'Test oral';
        }elseif(in_array($statut, ['Appointment canceled'])){
            $statutLabel = 'Rendez-vous annulé';
        }elseif(in_array($statut, ['Appointment rescheduled'])){
            $statutLabel = 'Rendez-vous reporté';
        }elseif(in_array($statut, ['Réservation Coaching'])){
            $statutLabel = 'Réservation Coaching';
        }else{
            $statutLabel = 'Autres';
        }

        return $statutLabel;
    }
}
