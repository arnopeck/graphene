<?php

namespace um;

class GroupNode extends \graphene\Node {
	
	
	function getMembersRecursive() {
		return $this->getType('User',__NAMESPACE__)->select('#x.groups.ancestors=?',$this);
	}
	
	function __toString() {
		return $this->groupName;
	}
	
	private function _initNode() {
		$this->data()->ancestors->add($this);
	}

	private function _onParentGroupInserted($id) {
		$g=$this->type()->getNode($id);
		$myDescendants=$this->data()->get('@ancestors');
		while( $g ) {
			foreach( $myDescendants as $d ) {
				$d->data()->ancestors->add($g);
			}
			$g=$g->parentGroup;
		}
	}
	
	private function _parentGroupValidator($id) {
		if( $this->get('@ancestors')->contains($id) ) throw new \Exception( 'Loop in group tree.' );
	}
	
	private function _onParentGroupUpdated($id,$oldId) {
		$this->_onParentGroupDeleted($oldId);
		$this->_onParentGroupInserted($id);
	}
	
	private function _onParentGroupDeleted($id) {
		$g=$this->type(__CLASS__)->getNode($id);
		$myDescendants=$this->data()->get('@ancestors');
		while( $g ) {
			foreach( $myDescendants as $d ) $d->data()->ancestors->remove($g);
			$g=$g->parentGroup;
		}
	}
	
	
	
}

