<?php

namespace Perspective\CustomerProductInfoGraphQl\Service;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\GroupRepositoryInterface;

class CurrentCustomer
{
    protected $context = [];

    protected $customerRepository;
    protected $groupRepository;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        GroupRepositoryInterface $groupRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->groupRepository = $groupRepository;
    }

    public function isUserLoggedIn(): bool
    {
        $customerId = $this->getCustomerId($this->context);

        if (!$customerId) {
            return false;
        }
        return true;
    }

    public function getCustomerId($context) // вызвать в ресолвере !первым
    {
        if (!$this->context) {
            $this->context = $context;
        }
        return $context->getUserId();
    }

    public function getCustomer(): CustomerInterface //try catch
    {
        $customerId = $this->getCustomerId($this->context);
        return $this->customerRepository->getById($customerId);
    }


    public function getCustomerGroupName(): string //try catch
    {
        return $this->groupRepository->getById($this->getCustomer()->getGroupId())->getCode();
    }
}
