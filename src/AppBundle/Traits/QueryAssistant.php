<?php
namespace AppBundle\Traits;

use Doctrine\ORM\QueryBuilder;

trait QueryAssistant
{
    protected function  queryParameters($params)
    {
        $res['searchField'] = (isset($params['sf'])) ? $params['sf'] : false;
        $res['searchValue'] = (isset($params['sv'])) ? $params['sv'] : '';

        $res['arrFilterField'] = (isset($params['ff'])) ? $params['ff'] : array();
        $res['arrSingleSearchField'] = (isset($params['ss'])) ? $params['ss'] : array();

        $res['arrRangeFilterField1'] = (isset($params['rff1'])) ? $params['rff1'] : array();
        $res['arrRangeFilterField2'] = (isset($params['rff2'])) ? $params['rff2'] : array();

        $res['orderField'] = (isset($params['orf'])) ? $params['orf'] : false;
        $res['order'] = (isset($params['ord'])) ? $params['ord'] : '';

        return $res;
    }

    public function searchQuery($qb, $arrSearchParam, $searchField, $searchValue)
    {
        if (!empty($searchValue))
        {
            if ($searchField != 'any') {
                $arrSearchValue = explode(" ", urldecode($searchValue));
                $i = 0;
                foreach ($arrSearchValue as $searchValue1) {
                    $qb->andWhere($qb->expr()->like($searchField, ':searchField'.$i));
                    $qb->setParameter('searchField'.$i, '%'.$searchValue1.'%');
                    $i++;
                }
            } else {
                $i = 0;
                foreach ($arrSearchParam as $item) {
                    if (!$i) {
                        $qb->andWhere($qb->expr()->like($item, ':searchField'.$i));
                        $qb->setParameter('searchField'.$i, '%'.$searchValue.'%');
                    } else {
                        $qb->orWhere($qb->expr()->like($item, ':searchField'.$i));
                        $qb->setParameter('searchField'.$i, '%'.$searchValue.'%');
                    }
                    $i++;
                }
            }
        }

        return $qb;
    }

    public function singleSearchQuery($qb, $arrSingleSearchParam)
    {
        if (!empty($arrSingleSearchParam)) {
            $i = 1;
            foreach ($arrSingleSearchParam as $field=>$val) {
                if ($val != '') {
                    $qb->andWhere($qb->expr()->like($field, ':singleSearchField'.$i));
                    $qb->setParameter('singleSearchField'.$i, '%'.$val.'%');
                }
                $i++;
            }
        }

        return $qb;
    }


    /**
     * @param $qb QueryBuilder
     * @param $arrFilterParam Array
     * @return QueryBuilder
     */
    public function filterQuery($qb, $arrFilterParam)
    {
        if (!empty($arrFilterParam)) {
            $i = 1;
            foreach ($arrFilterParam as $field=>$val) {
                if ($val != '') {

                    if (is_array($val)) {
                        $qb->andWhere("$field IN(:arrVal$i)");
                        $qb->setParameter("arrVal$i", array_values($val));
                    } else {
                        $arrVal = explode(",", $val);
                        if (count($arrVal) == 1) {
                            if ($val === '0') {
                                $qb->andWhere($qb->expr()->orX(
                                    $qb->expr()->eq($field, '0'),
                                    $qb->expr()->isNull($field)
                                ));
                            } else {
                                $qb->andWhere("$field = :filterField$i");
                                $qb->setParameter('filterField'.$i, $arrVal[0]);
                            }
                        } else {
                            $qb->andWhere("$field IN(:arrVal$i)");
                            $qb->setParameter("arrVal$i", array_values($arrVal));
                        }
                    }
                }
                $i++;
            }
        }

        return $qb;
    }

    public function rangeFilterQuery($qb, $arrRangeFilterParam1, $arrRangeFilterParam2)
    {
        if (!empty($arrRangeFilterParam1)) {
            $i = 1;
            foreach ($arrRangeFilterParam1 as $field=>$val1) {
                $val2 = $arrRangeFilterParam2[$field];
                if ($val1 != '' && $val2 != '') {
                    $qb->andWhere($qb->expr()->between($field, ":rangeValue1$i", ":rangeValue2$i"));
                    $qb->setParameter("rangeValue1$i", $this->dateFormatFilter(urldecode($val1)));
                    $qb->setParameter("rangeValue2$i", $this->dateFormatFilter(urldecode($val2), '23:59:59'));
                } else if ($val1 != '' && $val2 == '') {
                    $qb->andWhere("$field >= :rangeValueF1$i");
                    $qb->setParameter("rangeValueF1$i", $this->dateFormatFilter(urldecode($val1)));
                } else if ($val1 == '' && $val2 != '') {
                    $qb->andWhere("$field <= :rangeValueT2$i");
                    $qb->setParameter("rangeValueT2$i", $this->dateFormatFilter(urldecode($val2), '23:59:59'));
                }
                $i++;
            }
        }

        return $qb;
    }

    protected function dateFormatFilter($str = '', $timeFix = '00:00:00')
    {
        if (strtotime($str) !== false) {
            $date = new \DateTime($str);
            return $date->format('Y-m-d') . ' ' . $timeFix;
        }

        return $str;
    }
}