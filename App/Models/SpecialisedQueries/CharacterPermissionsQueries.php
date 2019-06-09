<?php

namespace App\Models\SpecialisedQueries;

use Illuminate\Database\Capsule\Manager as DB;

use App\Utilities\ArrayBasedSet;

class CharacterPermissionsQueries
{
	/**
	 * SELECT PermissionName
	 *     FROM WikiPermissions
	 *     INNER JOIN CharacterPermissionRelations
	 *         ON WikiPermissions.PermissionId = CharacterPermissionRelations.PermissionId
	 *     WHERE CharacterPermissionRelations.CharacterId = {$CharacterId};
	 */
	public static function getCharacterPermissions($characterId): object
	{
		$permissionNameStdClassArray = DB::table('WikiPermissions')
				->select('PermissionName')
				->join('CharacterPermissionRelations', 'WikiPermissions.PermissionId', '=', 'CharacterPermissionRelations.PermissionId')
				->where('CharacterPermissionRelations.CharacterId', $characterId)
				->get()->all();

		$permissionsArray = array();
		foreach( $permissionNameStdClassArray as $permissionNameStdClass )
			$permissionsArray[] = $permissionNameStdClass->PermissionName;

		$permissionsSet = new ArrayBasedSet($permissionsArray);
		return $permissionsSet;
	}

	/**
	 * Insert a collection of permissions, by name, to a character, by Id.
	 */
	public static function addCharacterPermissions($characterId, $permissions): void
	{
		if ( count($permissions) == 0 )
			return;

		if ( is_a($permissions, 'App\Utilities\SetInterface') )
			$permissions = $permissions->values();

		$permissionIdStdClassArray = DB::table('WikiPermissions')
				->select('PermissionId')
				->whereIn('PermissionName', $permissions)
				->get()->all();

		$insertion = array();
		foreach ( $permissionIdStdClassArray as $permissionIdStdClass )
			$insertion[] = ['CharacterId' => $characterId, 'PermissionId' => $permissionIdStdClass->PermissionId ];

		DB::table('CharacterPermissionRelations')->insert($insertion);
	}

	/**
	 * DELETE r FROM CharacterPermissionRelations r
	 *     INNER JOIN WikiPermissions p
	 *         ON p.`PermissionId` = r.`PermissionId`
	 *     WHERE r.`CharacterId` = {$CharacterId}
	 *     AND p.`PermissionName` IN ('permission1', 'permission2',...);
	 */
	public static function removeCharacterPermissions($characterId, $permissions): void
	{
		if ( count($permissions) == 0 )
			return;

		if ( is_a($permissions, 'App\Utilities\SetInterface') )
			$permissions = $permissions->values();

		DB::table('CharacterPermissionRelations')
				->join('WikiPermissions', 'CharacterPermissionRelations.PermissionId', '=', 'WikiPermissions.PermissionId')
				->where('CharacterPermissionRelations.CharacterId', $characterId)
				->whereIn('WikiPermissions.PermissionName', $permissions)
				->delete();
	}
}

