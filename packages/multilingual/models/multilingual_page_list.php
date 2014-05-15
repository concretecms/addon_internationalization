<?php defined('C5_EXECUTE') or die("Access Denied.");
Loader::model("page_list");
class MultilingualPageList extends PageList {

	protected function setBaseQuery($additionalFields = '') {
		$mslist = MultilingualSection::getList();
		$query = ',  (select mpRelationID from MultilingualPageRelations where cID = p1.cID LIMIT 1) as mpr';
		foreach ($mslist as $ms) {
        		$query .= ', (select count(mpRelationID) from MultilingualPageRelations where MultilingualPageRelations.mpRelationID = mpr and mpLocale = \'' . $ms->getLocale() . '\') as relationCount' . $ms->getCollectionID();
		}
		parent::setBaseQuery($query . $additionalFields);
	}

	public function filterByMissingTargets($targets) {
		$haveStr .= '';
		
		if (count($targets) > 0) {
			$haveStr .= '(';
		}
		
		for ($i = 0; $i < count($targets); $i++) {
			$t = $targets[$i];
			$haveStr .= 'relationCount' . $t->getCollectionID() . ' = 0';
			if (count($targets) > ($i + 1)) {
				$haveStr .= ' or ';
			}
		}

		if (count($targets) > 0) {
			$haveStr .= ')';
		}

		if ($haveStr) {
			$this->having(false, $haveStr);
		}	
	}
}
