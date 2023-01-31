<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2023
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Customer\Manager\Decorator;


/**
 * Disables changing demo password
 *
 * @package MShop
 * @subpackage Common
 */
class Demo
	extends \Aimeos\MShop\Common\Manager\Decorator\Base
{
	/**
	 * Deletes one or more items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface|\Aimeos\Map|array|string $items Item object, ID or a list of them
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function delete( $items ) : \Aimeos\MShop\Common\Manager\Iface
	{
        throw new \Aimeos\Admin\JQAdm\Exception( 'Deleting users is not allowed in demo' );
	}


	/**
	 * Adds or updates an item object.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $items Item object whose data should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\Map|\Aimeos\MShop\Common\Item\Iface Updated item including the generated ID
	 */
	public function save( $items, bool $fetch = true )
	{
        $customers = map( $items );
        $manager = $this->getManager();

        $filter = $manager->filter()->add( 'customer.id', '==', $customers->getId()->filter() )->slice( 0, 0xfffffff );
        $existing = $manager->search( $filter );

        foreach( $customers as $item )
        {
            if( $item->getId() && ( $exist = $existing->get( $item->getId() ) ) !== null
                && $item->getPassword() !== $exist->getPassword()
            ) {
                throw new \Aimeos\Admin\JQAdm\Exception( 'Changing passwords is not allowed in demo' );
            }
        }

		return $this->getManager()->save( $items, $fetch );
	}
}
