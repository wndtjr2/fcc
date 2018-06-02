<?php
/**
 * Created by PhpStorm.
 * User: swoogi
 * Date: 15. 1. 15.
 * Time: 오후 6:18
 */
namespace App\Auth;

use Cake\Auth\BaseAuthenticate;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\Error\Debugger;
use Cake\ORM\TableRegistry;
use App\Service\EncryptService;

class FccAuthenticate extends BaseAuthenticate
{
    /**
     * Checks the fields to ensure they are supplied.
     *
     * @param \Cake\Network\Request $request The request that contains login information.
     * @param array $fields The fields to be checked.
     * @return bool False if the fields have not been supplied. True if they exist.
     */
    protected function _checkFields(Request $request, array $fields)
    {

        foreach ([$fields['username'], $fields['type']] as $field) {
            $value = $request->data($field);
            if (empty($value) || !is_string($value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Authenticates the identity contained in a request. Will use the `config.userModel`, and `config.fields`
     * to find POST data that is used to find a matching record in the `config.userModel`. Will return false if
     * there is no post data, either username or password is missing, or if the scope conditions have not been met.
     *
     * @param \Cake\Network\Request $request The request that contains login information.
     * @param \Cake\Network\Response $response Unused response object.
     * @return mixed False on login failure.  An array of User data on success.
     */
    public function authenticate(Request $request, Response $response)
    {

        $fields = $this->_config['fields'];
        if (!$this->_checkFields($request, $fields)) {
            return ['result' => 'fail', 'code' => 'invalidfail'];
        }
        return $this->findUser(
            $request->data[$fields['username']],
            $request->data[$fields['type']],
            $request->data[$fields['password']]
        );

    }

    /**
     * Find a user record using the username and password provided.
     *
     * Input passwords will be hashed even when a user doesn't exist. This
     * helps mitigate timing attacks that are attempting to find valid usernames.
     *
     * @param string $username The username/identifier.
     * @param string normal:일반,facebook:페이스북,linkedin:링크드인,weibo:웨이보
     * @param string|null $password The password, if not provide password checking is skipped
     *   and result of find is returned.
     * @return bool|array Either false on failure, or an array of user data.
     */
    protected function findUser($username, $type, $password = null)
    {
        $result = $this->query($username)->first();
        if (empty($result)) {
            //조회하니 없음
            return ['result' => 'fail', 'code' => 'nouser'];
        }

        if ($type != $result->get($this->_config['fields']['type'])) {
            return ['result' => 'fail', 'code' => 'type', 'type' => $result->get($this->_config['fields']['type'])];
        }
        //일반 회원 인증은 패스워드를 비교함
        if ($type == 'normal') {
            $hasher = $this->passwordHasher();
            $hashedPassword = $result->get($this->_config['fields']['password']);
            if (!$hasher->check($password, $hashedPassword)) {
                return ['result' => 'fail', 'code' => 'password'];
            }

            $this->_needsPasswordRehash = $hasher->needsRehash($hashedPassword);
            $result->unsetProperty($this->_config['fields']['password']);
        }
        return $result->toArray() + ['result' => 'success'];
    }

    /**
     * Get query object for fetching user from database.
     *
     * @param string $username The username/identifier.
     * @return \Cake\ORM\Query
     */
    protected function query($username)
    {
        $encriptService = EncryptService::Instance();
        $username = $encriptService->encrypt($username);

        $config = $this->_config;
        $table = TableRegistry::get($this->_config['userModel']);
        $conditions = [$table->aliasField($config['fields']['username']) => $username];
        if ($config['scope']) {
            $conditions = array_merge($conditions, $config['scope']);
        }

        $query = $table->find('all')
            ->where($conditions);

        if ($config['contain']) {
            $query = $query->contain($config['contain']);
        }

        return $query;
    }
} 