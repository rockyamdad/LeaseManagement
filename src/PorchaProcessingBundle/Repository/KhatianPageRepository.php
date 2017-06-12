<?php

namespace PorchaProcessingBundle\Repository;
use Doctrine\ORM\EntityRepository;
use PorchaProcessingBundle\Entity\KhatianPage;
use PorchaProcessingBundle\Entity\KhatianVersion;

/**
 * KhatianPageRepository
 *
 */
class KhatianPageRepository extends EntityRepository
{
    public function mappedByField(KhatianPage $khatianPage)
    {
        $khatianPage->getType();//don't remove this, this is necessary

        $data = array();
        foreach ($this->getClassMetadata()->getColumnNames() as $column) {
            $data[$column] = $this->getClassMetadata()->getFieldValue(
                $khatianPage, $this->getClassMetadata()->getFieldName($column)
            );
        }
        return $data;
    }

    public function mappedByField1(KhatianPage $khatianPage, $data)
    {
        $khatianPage->getType();//don't remove this, this is necessary

        foreach ($this->getClassMetadata()->getColumnNames() as $column) {

            if (isset($this->getClassMetadata()->fieldNames[$column])) {
                $fieldName = $this->getClassMetadata()->getFieldName($column);
                if (array_key_exists($fieldName, $data)) {
                    $this->getClassMetadata()->setFieldValue($khatianPage, $fieldName, $data[$fieldName]);
                }
            }
        }

        return $khatianPage;
    }
} 