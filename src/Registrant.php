<?php

namespace IClimber\Zoom;

use Exception;
use Illuminate\Support\Collection;
use IClimber\Zoom\Exceptions\RequestException;
use IClimber\Zoom\Support\Model;

class Registrant extends Model
{
    public $type;
    public $relationshipID;

    const ENDPOINT = 'registrants';
    const NODE_NAME = 'registrant';
    const KEY_FIELD = 'id';

    protected $methods = ['get', 'post', 'patch', 'put', 'delete'];

    protected $attributes = [
        'id' => null, // string
        'email' => null, // string
        'first_name' => null, // string
        'last_name' => null, // string
        'address' => null, // string
        'city' => null, // string
        'country' => null, // string
        'zip' => null, // string
        'state' => null, // string
        'phone' => null, // string
        'industry' => null, // string
        'org' => null, // string
        'job_title' => null, // string
        'purchasing_time_frame' => null, // string
        'role_in_purchase_process' => null, // string
        'no_of_employees' => null, // string
        'comments' => null, // string
        'custom_questions' => [],
        'status' => null, // string
        'create_time' => null, // string [date-time]
        'join_url' => null, // string [string]
    ];

    protected $createAttributes = [
        'email',
        'first_name',
        'last_name',
        'address',
        'city',
        'country',
        'zip',
        'state',
        'phone',
        'industry',
        'org',
        'job_title',
        'purchasing_time_frame',
        'role_in_purchase_process',
        'no_of_employees',
        'comments',
        'custom_questions',
    ];

    protected $updateAttributes = [

    ];

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setRelationshipID($id)
    {
        $this->relationshipID = $id;
    }

    public function make($attributes)
    {
        $model = new static($this->client);
        $model->fill($attributes);
        if (isset($this->type)) {
            $model->setType($this->type);
        }

        if (isset($this->relationshipID)) {
            $model->setRelationshipID($this->relationshipID);
        }

        return $model;
    }

    public function get()
    {
        if (in_array('get', $this->methods)) {
            $this->response = $this->client->get("{$this->type}/{$this->relationshipID}/{$this->getEndpoint()}{$this->getQueryString()}");
            if ($this->response->getStatusCode() == 200) {
                return $this->collect($this->response->getBody());
            } else {
                throw $this->getExceptionByResponse($this->response);
            }
        }
    }

    public function all($fromPage = 1): Collection
    {
        if (!is_null($this->relationshipID)) {
            return $this->collect($this->get("{$this->type}/{$this->relationshipID}/{$this->getEndpoint()}"));
        } else {
            throw new Exception('No Relationship set');
        }
    }

    public function save()
    {
        if ($this->hasID()) {
            if (in_array('put', $this->methods) || in_array('patch', $this->methods)) {
                $this->response = $this->client->patch("{$this->type}/{$this->relationshipID}/{$this->getEndpoint()}", $this->updateAttributes());
                if ($this->response->getStatusCode() == 204) {
                    return $this;
                } else {
                    throw $this->getExceptionByResponse($this->response);
                }
            }
        } else {
            if (in_array('post', $this->methods)) {
                $this->response = $this->client->post("{$this->type}/{$this->relationshipID}/{$this->getEndpoint()}", $this->createAttributes());
                if ($this->response->getStatusCode() == 201) {
                    $this->fill($this->response->getBody());

                    return $this;
                } else {
                    throw $this->getExceptionByResponse($this->response);
                }
            }
        }
    }

    public function delete($id = '')
    {
        return $this->cancel();
    }

    public function cancel()
    {
        $this->response = $this->client->put("/{$this->type}/{$this->relationshipID}/registrants/status", ['action' => 'cancel', 'registrant' => [['email' => $this->email]]]);
        if ($this->response->getStatusCode() == 204) {
            return $this->response->getBody();
        } else {
            throw $this->getExceptionByResponse($this->response);
        }
    }

    public function deny()
    {
        $this->response = $this->client->put("/{$this->type}/{$this->relationshipID}/registrants/status", ['action' => 'deny', 'registrant' => [['email' => $this->email]]]);
        if ($this->response->getStatusCode() == 204) {
            return $this->response->getBody();
        } else {
            throw $this->getExceptionByResponse($this->response);
        }
    }

    public function approve()
    {
        $this->response = $this->client->put("/{$this->type}/{$this->relationshipID}/registrants/status", ['action' => 'approve', 'registrant' => [['email' => $this->email]]]);
        if ($this->response->getStatusCode() == 204) {
            return $this->response->getBody();
        } else {
            throw $this->getExceptionByResponse($this->response);
        }
    }
}
