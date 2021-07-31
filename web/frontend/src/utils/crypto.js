/**
 * frontendPasswordHashPrefix
 *
 * Need not be long; simply guarantees the resulting SHA256 hash does not look
 * exactly like that of the normal SHA256 of the password. Therefore, if this
 * hash is captured, the attacker does not have a standard legitimate hash of
 * the password hash, which would feasibly have been able to be re-used
 * elsewhere.
 *
 * It's not necessary, but it doesn't hurt.
 */
const frontendPasswordHashPrefix = 'a72b9e12';

/**
 * A password hash is created for a few reasons.
 *
 * - If accidental logging occurs (e.g. of POST parameters), this avoids us
 *   seeing the actual password of a legitimate user.
 *
 * - A machine-in-the-middle doesn't directly receive the password of the user;
 *   while SHA256 is not appropriate as a password hash, this will prevent
 *   pivoting when the password hashed is strong.
 *
 * It is not created to act as a backend database hash. This is done separately,
 * as appropriate.
 */
export async function computePasswordHash(password)
{
	// Take a modified SHA256 hash of the password provided to create frontend
	// hash.
	const passwordFrontendHash = await sha256hex(frontendPasswordHashPrefix + values.password);

	return passwordFrontendHash;
}

export async function sha256hex(strIn)
{
	// Encode input string as UTF-8
	const bytes = new TextEncoder().encode(strIn);                    

	// SHA-256 hash
	const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);

	// Convert ArrayBuffer to array of bytes
	const hashArray = Array.from(new Uint8Array(hashBuffer));

	// Convert bytes to hex string                  
	const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');

	return hashHex;
}
